#!/usr/bin/env bash
#
# install-phalcon.sh — Install Phalcon 5.x on any Debian/Ubuntu-based
# system (Lando container, VPS, Docker) with a single command.
#
# Dispatched by: tavp phalcon:install
#
# Usage:
#   sudo bash install-phalcon.sh            # auto-detect PHP version
#   sudo bash install-phalcon.sh 8.3        # pin a PHP version
#   sudo bash install-phalcon.sh 8.3 5.16.0 # pin PHP + Phalcon version
#
# Idempotent: exits early if Phalcon is already loaded.
#
set -euo pipefail

PHP_VERSION="${1:-$(php -r 'echo PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION;' 2>/dev/null || echo 8.3)}"
PHALCON_VERSION="${2:-5.16.0}"

TOTAL_STEPS=7
STEP=0
SCRIPT_START="$(date +%s)"

# --- helpers ------------------------------------------------------------

# Human-friendly elapsed time between $1 (epoch) and now: "2m05s" / "45s".
fmt_elapsed() {
    local start="$1" now diff m s
    now="$(date +%s)"
    diff=$(( now - start ))
    m=$(( diff / 60 )); s=$(( diff % 60 ))
    if [ "${m}" -gt 0 ]; then printf '%dm%02ds' "${m}" "${s}"; else printf '%ds' "${s}"; fi
}

# Print a numbered step header: "==> [3/7] message".
step() {
    STEP=$(( STEP + 1 ))
    echo ""
    echo "==> [${STEP}/${TOTAL_STEPS}] $1"
}

# Run a command with an animated spinner + live elapsed timer.
# Output is captured to a log; on failure the tail is printed.
#   spin "Compiling..." make -j4
spin() {
    local msg="$1"; shift
    local logf start pid rc i spinchars
    logf="$(mktemp)"
    start="$(date +%s)"

    ( "$@" ) >"${logf}" 2>&1 &
    pid=$!

    spinchars='|/-\'
    i=0
    while kill -0 "${pid}" 2>/dev/null; do
        i=$(( (i + 1) % 4 ))
        printf "\r    %s %s  [%s]   " "${spinchars:$i:1}" "${msg}" "$(fmt_elapsed "${start}")"
        sleep 0.2
    done

    if wait "${pid}"; then rc=0; else rc=$?; fi

    if [ "${rc}" -eq 0 ]; then
        printf "\r    \342\234\223 %s  [%s]              \n" "${msg}" "$(fmt_elapsed "${start}")"
        rm -f "${logf}"
    else
        printf "\r    \342\234\227 %s  [%s]              \n" "${msg}" "$(fmt_elapsed "${start}")"
        echo "    ---- last 25 lines of output ----"
        sed 's/^/    /' "${logf}" | tail -25
        rm -f "${logf}"
        exit "${rc}"
    fi
}

echo "======================================================"
echo " TAVP Phalcon installer"
echo "    Phalcon : ${PHALCON_VERSION}"
echo "    PHP     : ${PHP_VERSION}"
echo "======================================================"

# --- Preamble: idempotency, root, OS -----------------------------------
if php -m 2>/dev/null | grep -qi '^phalcon$'; then
    echo "==> Phalcon is already installed. Nothing to do."
    exit 0
fi

if [ "$(id -u)" -ne 0 ]; then
    echo "ERROR: this script needs root. Run with sudo or inside a root container." >&2
    exit 1
fi

if [ -f /etc/os-release ]; then
    # shellcheck disable=SC1091
    . /etc/os-release
fi
case "${ID:-unknown}" in
    ubuntu|debian|raspbian) echo "==> Detected Debian/Ubuntu family." ;;
    *) echo "WARNING: untested OS '${ID:-unknown}'. Proceeding anyway." >&2 ;;
esac

# --- [1/7] Build dependencies ------------------------------------------
step "Installing build dependencies..."
export DEBIAN_FRONTEND=noninteractive
spin "apt-get update" apt-get update -y

install_deps() {
    apt-get install -y wget git curl build-essential autoconf pkg-config re2c zlib1g-dev
    # PCRE dev headers: libpcre2-dev on Trixie+, libpcre3-dev on older.
    apt-get install -y libpcre2-dev || apt-get install -y libpcre3-dev || true
    # PHP dev headers when available via apt (source-built PHP already has them).
    apt-get install -y "php${PHP_VERSION}-dev" || true
    apt-get install -y "php${PHP_VERSION}-xml" || true
}
spin "Installing packages" install_deps

# --- [2/7] Detect PHP toolchain ----------------------------------------
step "Detecting PHP toolchain..."
PHPIZE="phpize${PHP_VERSION}"
PHPCONFIG="php-config${PHP_VERSION}"
command -v "${PHPIZE}" >/dev/null 2>&1 || PHPIZE="phpize"
command -v "${PHPCONFIG}" >/dev/null 2>&1 || PHPCONFIG="php-config"
if ! command -v "${PHPIZE}" >/dev/null 2>&1; then
    echo "ERROR: phpize not found. Install php${PHP_VERSION}-dev or a PHP with dev headers." >&2
    exit 1
fi
echo "    phpize     : $(command -v "${PHPIZE}")"
echo "    php-config : $(command -v "${PHPCONFIG}")"

# --- [3/7] Download source ---------------------------------------------
step "Downloading Phalcon ${PHALCON_VERSION} source..."
WORKDIR="$(mktemp -d)"
cd "${WORKDIR}"

# PECL is the reliable source (its tarball always ships a config.m4).
# Fall back to the GitHub release asset if PECL is unreachable.
if ! wget -q --show-progress "https://pecl.php.net/get/phalcon-${PHALCON_VERSION}.tgz" -O phalcon.tgz; then
    echo "==> PECL unreachable, trying GitHub release..."
    wget -q --show-progress "https://github.com/phalcon/cphalcon/releases/download/v${PHALCON_VERSION}/phalcon-${PHALCON_VERSION}.tgz" \
        -O phalcon.tgz \
    || wget -q --show-progress "https://github.com/phalcon/cphalcon/releases/download/v${PHALCON_VERSION}/phalcon-pecl.tgz" \
        -O phalcon.tgz
fi

if [ ! -s phalcon.tgz ]; then
    echo "ERROR: failed to download Phalcon ${PHALCON_VERSION} source." >&2
    exit 1
fi

tar -xzf phalcon.tgz

# Locate the actual build root by finding the shallowest config.m4.
# Tarballs may extract as phalcon-X.Y.Z/, cphalcon-X.Y.Z/, or build/php*/.
CONFIG_M4="$(find "${WORKDIR}" -name config.m4 -printf '%d %p\n' 2>/dev/null | sort -n | head -1 | cut -d' ' -f2-)"
if [ -z "${CONFIG_M4}" ]; then
    echo "ERROR: config.m4 not found after extracting Phalcon source." >&2
    exit 1
fi
BUILD_DIR="$(dirname "${CONFIG_M4}")"
echo "    Build dir  : ${BUILD_DIR}"
cd "${BUILD_DIR}"

# --- [4/7] Prepare build (phpize + configure) --------------------------
step "Preparing build (phpize + configure)..."
spin "phpize" "${PHPIZE}"
spin "configure" ./configure --with-php-config="${PHPCONFIG}"

# --- [5/7] Compile -----------------------------------------------------
step "Compiling Phalcon (this can take 3-10 minutes)..."
spin "make -j$(nproc)" make -j"$(nproc)"

# --- [6/7] Install + enable --------------------------------------------
step "Installing extension..."
spin "make install" make install

EXT_DIR="$(${PHPCONFIG} --extension-dir)"
INI_DIR="$(${PHPCONFIG} --ini-dir 2>/dev/null || echo /etc/php/${PHP_VERSION}/cli/conf.d)"
mkdir -p "${INI_DIR}"
cat > "${INI_DIR}/30-phalcon.ini" <<EOF
extension=${EXT_DIR}/phalcon.so
EOF

for sapi in apache2 fpm cli; do
    if [ -d "/etc/php/${PHP_VERSION}/${sapi}/conf.d" ]; then
        cp "${INI_DIR}/30-phalcon.ini" "/etc/php/${PHP_VERSION}/${sapi}/conf.d/30-phalcon.ini"
    fi
done

# Lando / Docker: always copy to shared conf.d so both CLI and web server see it.
if [ -d "/usr/local/etc/php/conf.d" ]; then
    cp "${INI_DIR}/30-phalcon.ini" "/usr/local/etc/php/conf.d/30-phalcon.ini"
    echo "    Enabled in : /usr/local/etc/php/conf.d/30-phalcon.ini"
else
    echo "    Enabled in : ${INI_DIR}/30-phalcon.ini"
fi

# Restart web server so the extension is loaded by the web SAPI.
if command -v apachectl >/dev/null 2>&1; then
    apachectl restart 2>/dev/null || true
fi
if command -v service >/dev/null 2>&1; then
    service php${PHP_VERSION}-fpm restart 2>/dev/null || true
fi

# --- [7/7] Verify ------------------------------------------------------
step "Verifying..."
if php -m | grep -qi '^phalcon$'; then
    echo ""
    echo "======================================================"
    echo " \342\234\223 SUCCESS: Phalcon ${PHALCON_VERSION} installed for PHP ${PHP_VERSION}."
    echo "   Total time: $(fmt_elapsed "${SCRIPT_START}")"
    echo "======================================================"
else
    echo "ERROR: Phalcon installed but not detected by php -m." >&2
    exit 1
fi

cd /
rm -rf "${WORKDIR}"
