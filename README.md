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
| `tavp make:model <Name>` | Generate model → `app/Models` (`--migration`, `--resource`) |
| `tavp make:controller <Name>` | Generate controller → `app/Controllers` (`--api`, `--resource`) |
| `tavp make:migration <Name>` | Generate migration → `database/migrations` (`--table=nama`) |
| `tavp make:view <name>` | Generate Volt view → `resources/views` (`--layout=`) |
| `tavp make:seeder <Name>` | Generate seeder → `app/Database/Seeders` |
| `tavp make:middleware <Name>` | Generate middleware → `app/Middleware` |
| `tavp make:event <Name>` | Generate event → `app/Events` |
| `tavp make:listener <Name>` | Generate listener → `app/Listeners` (`--event=`) |
| `tavp make:module <name>` | Scaffold module → `modules/<name>` |
| `tavp make:scaffold <Name>` | Generate model + migration + controller + views + route sekaligus |
| `tavp migrate` | Jalankan / rollback / fresh / seed migration (`--rollback`, `--fresh`, `--status`, `--seed`, `--step=N`) |
| `tavp migrate:status` | Tampilkan migration mana yang sudah jalan / pending |
| `tavp key:generate` | Generate APP_KEY and JWT_SECRET |
| `tavp phalcon:install` | Install Phalcon C-extension |
| `tavp deploy` | Deploy to production server |
| `tavp env:list` | List configured environment adapters |
| `tavp help` | Show available commands |

> Semua `make:*` menulis file ke **project root** (folder tempat kamu jalankan `tavp`), dengan namespace `App\` sesuai autoload PSR-4 project. Tidak pernah menulis ke `vendor/`.

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
