# Internal Contracts

Tidak ada REST API publik. Komunikasi antar bagian memakai helper/service di `/includes`.

## Endpoint Internal

| Endpoint | Method | Input | Output |
|---|---|---|---|
| `/admin/scan-process.php` | POST | `kode`, `tahap`, `csrf_token` | JSON hasil scan |
| `/admin/send-barcode.php` | POST | `peserta_id`, `csrf_token` | Redirect + flash message |
| `/admin/cetak-gelang.php` | POST | `peserta_id`, `csrf_token` | Halaman siap cetak |

Parameter `tahap` bernilai `daftar_ulang` atau `masuk`, menentukan kolom mana yang diperbarui.

### Respons `scan-process.php`

```json
{
  "status": "success | duplicate | belum_daftar_ulang | invalid",
  "tahap": "daftar_ulang | masuk",
  "nama": "Nama Peserta",
  "gereja": "Asal Gereja",
  "kode": "EVT-000001",
  "waktu": "2026-08-07 09:15:00",
  "gelang_dicetak": false,
  "message": "Daftar ulang berhasil"
}
```

Nilai `belum_daftar_ulang` hanya muncul pada tahap 2, ketika peserta mencoba masuk ruangan tanpa melewati meja daftar ulang.

Seluruh endpoint wajib memeriksa session admin dan CSRF token sebelum memproses.

## Service Helper

| Service | Fungsi Utama |
|---|---|
| `mailer.php` | `kirimBarcodePeserta($peserta)` mengembalikan boolean |
| `barcode.php` | `buatBarcodePeserta($kode)` mengembalikan path file gambar |
| `functions.php` | Sanitasi, escape output, CSRF token, format tanggal |
| `auth.php` | Proteksi halaman admin |
| `database.php` | Koneksi PDO/MySQLi dengan prepared statements |