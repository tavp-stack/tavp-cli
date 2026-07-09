# tavp-cli

Command line resmi TAVP. Namanya `tavp`. Fungsinya bikin hidup developer
(juga yang bukan programmer) jadi gampang: nggak perlu sentuh struktur folder
manual, tinggal jalanin perintah.

## Perintah utama

| Perintah | Fungsi |
|---|---|
| `tavp new` | Wizard bikin project baru (template, modul, database, mail) |
| `tavp make:migration` | Generate file migration |
| `tavp make:scaffold` | Generate model + controller + view + migration + route sekaligus |
| `tavp migrate` | Jalankan / rollback / fresh / seed migration |
| `tavp module:install` | Pasang module dari Composer, jalanin migration & publish asset |
| `tavp module:publish` | Upload module buatan lo ke marketplace |

## Filosofi

Sekuil mungkin langkah manual. Lo arahin, AI atau dev jalanin perintahnya.
Cocok buat non-programmer yang mau deploy sendiri tanpa ngoding.

## Status

Planning.
