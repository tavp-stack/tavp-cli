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

echo "==> TAVP Phalcon installer"
echo "    Phalcon : ${PHALCON_VERSION}"
echo "    PHP     : ${PHP_VERSION}"

# --- 1. Already installed? ---------------------------------------------
if php -m 2>/dev/null | grep -qi '^phalcon$'; then
    echo "==> Phalcon is already installed. Nothing to do."
    exit 0
fi

# --- 2. Root / sudo check ----------------------------------------------
if [ "$(id -u)" -ne 0 ]; then
    echo "ERROR: this script needs root. Run with sudo or inside a root container." >&2
    exit 1
fi

# --- 3. OS family -------------------------------------------------------
if [ -f /etc/os-release ]; then
    # shellcheck disable=SC1091
    . /etc/os-release
fi

case "${ID:-unknown}" in
    ubuntu|debian|raspbian) echo "==> Detected Debian/Ubuntu family." ;;
    *) echo "WARNING: untested OS '${ID:-unknown}'. Proceeding anyway." >&2 ;;
esac

# --- 4. Build dependencies ---------------------------------------------
echo "==> Installing build dependencies..."
export DEBIAN_FRONTEND=noninteractive
apt-get update -y

# Core build tools (required)
apt-get install -y \
    wget git curl build-essential autoconf pkg-config re2c zlib1g-dev

# PCRE dev headers: libpcre2-dev on modern Debian/Ubuntu (Trixie+),
# libpcre3-dev on older releases. Try the modern one first.
apt-get install -y libpcre2-dev || apt-get install -y libpcre3-dev || true

# PHP dev headers (phpize/php-config live here). php-pear is not needed
# because we compile from source, and it was dropped in Debian Trixie.
apt-get install -y "php${PHP_VERSION}-dev" || true
apt-get install -y "php${PHP_VERSION}-xml" || true

PHPIZE="phpize${PHP_VERSION}"
PHPCONFIG="php-config${PHP_VERSION}"
command -v "${PHPIZE}" >/dev/null 2>&1 || PHPIZE="phpize"
command -v "${PHPCONFIG}" >/dev/null 2>&1 || PHPCONFIG="php-config"

# --- 5. Download source -------------------------------------------------
echo "==> Downloading Phalcon ${PHALCON_VERSION} source..."
WORKDIR="$(mktemp -d)"
cd "${WORKDIR}"

# PECL is the reliable source (its tarball always ships a config.m4).
# Fall back to the GitHub release asset if PECL is unreachable.
if ! wget -q "https://pecl.php.net/get/phalcon-${PHALCON_VERSION}.tgz" -O phalcon.tgz; then
    echo "==> PECL unreachable, trying GitHub release..."
    wget -q "https://github.com/phalcon/cphalcon/releases/download/v${PHALCON_VERSION}/phalcon-${PHALCON_VERSION}.tgz" \
        -O phalcon.tgz \
    || wget -q "https://github.com/phalcon/cphalcon/releases/download/v${PHALCON_VERSION}/phalcon-pecl.tgz" \
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
echo "==> Build directory: ${BUILD_DIR}"
cd "${BUILD_DIR}"

# --- 6. Compile --------------------------------------------------------
echo "==> Compiling Phalcon (this takes a few minutes)..."
"${PHPIZE}"
./configure --with-php-config="${PHPCONFIG}"
make -j"$(nproc)"

# --- 7. Install + enable -----------------------------------------------
echo "==> Installing extension..."
make install

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

# --- 8. Verify ---------------------------------------------------------
echo "==> Verifying..."
if php -m | grep -qi '^phalcon$'; then
    echo "==> SUCCESS: Phalcon ${PHALCON_VERSION} installed for PHP ${PHP_VERSION}."
else
    echo "ERROR: Phalcon installed but not detected by php -m." >&2
    exit 1
fi

cd /
rm -rf "${WORKDIR}"
echo "==> Done."
