# Validation

Validasi wajib dilakukan di sisi server. Validasi HTML5 di browser hanya pelengkap.

## Aturan per Field

| Field | Aturan |
|---|---|
| `nama` | Wajib, 3–100 karakter, setelah `trim` |
| `gereja` | Wajib, 3–150 karakter |
| `info_dari` | Wajib, harus salah satu opsi yang tersedia; bila "Lainnya" maka isian teks wajib diisi |
| `email` | Wajib, format valid (`FILTER_VALIDATE_EMAIL`), unik di tabel `peserta` |
| `whatsapp` | Wajib, hanya angka setelah dinormalisasi, panjang 10–15 digit |

## Normalisasi

- Semua input di-`trim` sebelum divalidasi dan disimpan.
- Email disimpan dalam huruf kecil.
- Nomor WhatsApp dibersihkan dari spasi, tanda hubung, dan tanda kurung. Awalan `0` dikonversi ke `62`.

## Penanganan Error

- Bila validasi gagal, form ditampilkan kembali dengan pesan error per field.
- Nilai yang sudah diisi peserta tetap dipertahankan (kecuali field yang sensitif).
- Email duplikat menampilkan pesan khusus: peserta sudah terdaftar dan diminta memeriksa email atau menghubungi panitia.
- Semua pesan error ditampilkan dalam bahasa Indonesia.