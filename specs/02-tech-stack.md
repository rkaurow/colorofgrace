# Tech Stack

| Komponen | Pilihan | Keterangan |
|---|---|---|
| Bahasa | PHP 8+ | Native, tanpa framework |
| UI | Bootstrap 5 | Via CDN |
| Database | MySQL | InnoDB, utf8mb4 |
| Email | PHPMailer | SMTP Gmail App Password / Brevo |
| Barcode | `picqer/php-barcode-generator` | Barcode 1D **Code 128**, output PNG |
| Scanner | Mesin barcode USB (keyboard wedge) | Perangkat keras, tanpa driver khusus |
| Scanner cadangan | `html5-qrcode` | Opsional, hanya bila mesin barcode tidak tersedia |
| Hosting | InfinityFree | Shared hosting, tanpa akses Composer |

## Catatan

- Library barcode diunggah manual ke `/vendor` karena InfinityFree tidak menyediakan Composer.
- Pastikan library yang dipilih tidak bergantung pada ekstensi PHP yang dinonaktifkan di shared hosting. Output PNG memerlukan ekstensi **GD**.
- Panitia disarankan memakai scanner tipe **image/2D**, bukan laser, agar barcode tetap terbaca dari layar ponsel.