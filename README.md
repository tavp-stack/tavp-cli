# tavp-cli

Command line resmi TAVP. Namanya `tavp`. Fungsinya bikin hidup developer
(juga yang bukan programmer) jadi gampang: nggak perlu sentuh struktur folder
manual, tinggal jalanin perintah.

## Requirements

- PHP 8.3+
- Phalcon 5.16+ (C-extension)
- Composer

## Install

```bash
# Global install (recommended)
composer global require tavp/cli

# Or per-project
composer require tavp/cli
```

## Commands

| Command | Description |
|---|---|
| `tavp new` | Wizard bikin project baru (template, modul, database, mail) |
| `tavp serve` | Start development server |
| `tavp make:model` | Generate model file |
| `tavp make:controller` | Generate controller file |
| `tavp make:migration` | Generate migration file |
| `tavp make:scaffold` | Generate model + controller + view + migration + route sekaligus |
| `tavp migrate` | Jalankan / rollback / fresh / seed migration (`--rollback`, `--fresh`, `--status`, `--seed`, `--step=N`) |
| `tavp migrate:status` | Tampilkan migration mana yang sudah jalan / pending |
| `tavp key:generate` | Generate APP_KEY and JWT_SECRET |
| `tavp phalcon:install` | Install Phalcon C-extension |
| `tavp deploy` | Deploy to production server |
| `tavp env:list` | List configured environment adapters |
| `tavp help` | Show available commands |

## Philosophy

Sekuil mungkin langkah manual. Lo arahin, AI atau dev jalanin perintahnya.
Cocok buat non-programmer yang mau deploy sendiri tanpa ngoding.

## Testing

```bash
composer test
```

## Status

Part of **TAVP Stack 1.0.0 (Stable)**. Public API locked. SemVer applies.

## License

MIT
