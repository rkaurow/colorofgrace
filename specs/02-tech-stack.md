# Tech Stack

| Komponen | Pilihan | Keterangan |
|---|---|---|
| Bahasa | PHP 8+ | Native, tanpa framework |
| UI | Bootstrap 5 | Via CDN |
| Database | MySQL | InnoDB, utf8mb4 |
| Email | PHPMailer | SMTP Gmail App Password / Brevo |
| QR Code | `chillerlan/php-qrcode` | QR Code 2D, output PNG |
| Scanner | Scanner image/2D USB (keyboard wedge) | Perangkat keras, tanpa driver khusus |
| Scanner cadangan | Input kode manual | Opsional, bila scanner USB tidak tersedia |
| Hosting | InfinityFree | Shared hosting, tanpa akses Composer |

## Catatan

- Library QR Code dan PHPMailer diunggah manual ke `/vendor` karena InfinityFree tidak menyediakan Composer.
- Pastikan library yang dipilih tidak bergantung pada ekstensi PHP yang dinonaktifkan di shared hosting. Output PNG memerlukan ekstensi **GD**.
- Panitia disarankan memakai scanner tipe **image/2D**, bukan laser, agar QR Code tetap terbaca dari layar ponsel.