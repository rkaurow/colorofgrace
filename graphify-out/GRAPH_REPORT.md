# Graph Report - coiministry  (2026-08-10)

## Corpus Check
- 55 files · ~367,697 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 250 nodes · 247 edges · 51 communities (40 shown, 11 thin omitted)
- Extraction: 89% EXTRACTED · 11% INFERRED · 0% AMBIGUOUS · INFERRED: 26 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- functions.php
- auth.php
- Panduan Deploy ke VPS
- mailer.php
- barcode.php
- Instalasi di VPS (Ubuntu 22.04 / 24.04)
- COI Ministry — Sistem Registrasi & Check-in Acara
- Check-in Flow
- Barcode Generation
- Testing Checklist
- Participants Module
- Wristband Printing
- Database
- Registration Flow
- Email Service
- Admin Dashboard
- Authentication
- UI Components
- Validation
- Internal Contracts
- Deployment
- Definition of Done
- Goals
- Tech Stack
- 00-context.md
- 03-folder-structure.md
- 04-coding-standards.md
- 05-security.md
- 16-error-handling.md
- 23-backlog.md
- bugfix.md
- build.md
- refactor.md
- review.md

## God Nodes (most connected - your core abstractions)
1. `Panduan Deploy ke VPS` - 18 edges
2. `Instalasi di VPS (Ubuntu 22.04 / 24.04)` - 15 edges
3. `db()` - 10 edges
4. `COI Ministry — Sistem Registrasi & Check-in Acara` - 9 edges
5. `Check-in Flow` - 9 edges
6. `kirim_barcode_peserta()` - 7 edges
7. `kirim_email_ditolak()` - 7 edges
8. `Barcode Generation` - 7 edges
9. `Testing Checklist` - 7 edges
10. `setujui_peserta()` - 6 edges

## Surprising Connections (you probably didn't know these)
- `admin_login()` --calls--> `db()`  [INFERRED]
  includes/auth.php → config/database.php
- `setujui_peserta()` --calls--> `buat_barcode_peserta()`  [INFERRED]
  admin/approval.php → includes/barcode.php
- `setujui_peserta()` --calls--> `kirim_barcode_peserta()`  [INFERRED]
  admin/approval.php → includes/mailer.php
- `tolak_peserta()` --calls--> `kirim_email_ditolak()`  [INFERRED]
  admin/approval.php → includes/mailer.php
- `jumlah_diterima()` --calls--> `db()`  [INFERRED]
  includes/functions.php → config/database.php

## Import Cycles
- None detected.

## Communities (51 total, 11 thin omitted)

### Community 0 - "functions.php"
Cohesion: 0.10
Nodes (17): setujui_peserta(), tolak_peserta(), url_hal(), db(), cari_peserta_by_id(), cari_peserta_by_kode(), csrf_field(), csrf_require() (+9 more)

### Community 1 - "auth.php"
Cohesion: 0.16
Nodes (7): admin_logged_in(), admin_login(), require_admin(), require_admin_json(), flash_set(), json_response(), redirect()

### Community 2 - "Panduan Deploy ke VPS"
Cohesion: 0.11
Nodes (18): 0. Cek server, 10. Pasang SSL (HTTPS), 11. Buat akun admin, 12. Aktifkan email, 13. Uji sebelum sebar link, 14. Backup otomatis, 1. Update & pasang paket, 2. Amankan MySQL (+10 more)

### Community 3 - "mailer.php"
Cohesion: 0.44
Nodes (10): e(), kirim_barcode_peserta(), kirim_email_ditolak(), kirim_email_pending(), kirim_polos_phpmailer(), kirim_via_mail(), kirim_via_phpmailer(), muat_phpmailer() (+2 more)

### Community 4 - "barcode.php"
Cohesion: 0.46
Nodes (7): barcode_data_uri(), buat_barcode_peserta(), code128_encode(), code128_ke_png(), code128_patterns(), url_barcode_peserta(), normalisasi_kode()

### Community 22 - "Instalasi di VPS (Ubuntu 22.04 / 24.04)"
Cohesion: 0.11
Nodes (18): 0. Masuk ke server, 10. Arahkan domain, 11. Pasang SSL (HTTPS) — Gratis dengan Let's Encrypt, 12. Buat akun admin, 13. Verifikasi akhir, 1. Update sistem & pasang semua paket, 2. Amankan MySQL, 3. Buat database & user khusus (+10 more)

### Community 23 - "COI Ministry — Sistem Registrasi & Check-in Acara"
Cohesion: 0.17
Nodes (11): Alur Sistem, Catatan Perangkat Keras, COI Ministry — Sistem Registrasi & Check-in Acara, Instalasi Lokal (Pengembangan), Keamanan, Kebutuhan, Kebutuhan Server, Langkah (+3 more)

### Community 24 - "Check-in Flow"
Cohesion: 0.17
Nodes (11): Alur Lengkap di Lapangan, Aturan Penting, Cadangan, Cara Kerja Mesin Barcode, Check-in Flow, Logika Validasi Tahap 1, Logika Validasi Tahap 2, Perilaku Field Scan (+3 more)

### Community 25 - "Barcode Generation"
Cohesion: 0.25
Nodes (7): Barcode Generation, Dua Media Barcode, Format Kode, Keterbacaan dari Layar Ponsel, Penyimpanan, Simbologi, Spesifikasi Gambar untuk Email

### Community 26 - "Testing Checklist"
Cohesion: 0.25
Nodes (7): Admin, Cetak Gelang, Pendaftaran, Tahap 1 — Daftar Ulang, Tahap 2 — Masuk Ruangan, Testing Checklist, Umum

### Community 27 - "Participants Module"
Cohesion: 0.29
Nodes (6): Cetak Gelang, Fitur Pendukung, Kirim Barcode, Kolom Tabel, Participants Module, Tombol Aksi

### Community 28 - "Wristband Printing"
Cohesion: 0.29
Nodes (6): Alur Cetak, Aturan Penting, Dimensi Barcode di Gelang, Isi Gelang, Spesifikasi Cetak, Wristband Printing

### Community 29 - "Database"
Cohesion: 0.33
Nodes (5): Database, DDL, Dua Tahap Kehadiran, Tabel `admin`, Tabel `peserta`

### Community 30 - "Registration Flow"
Cohesion: 0.33
Nodes (5): Aturan Penting, Field Form, Langkah Pengguna, Registration Flow, Urutan Proses Server

### Community 31 - "Email Service"
Cohesion: 0.33
Nodes (5): Aturan Tampilan Barcode di Email, Email Service, Isi Email, Pemicu Pengiriman, Penanganan Kegagalan

### Community 32 - "Admin Dashboard"
Cohesion: 0.33
Nodes (5): Admin Dashboard, Alur Admin, Halaman Admin, Kartu Statistik, Navigasi

### Community 33 - "Authentication"
Cohesion: 0.40
Nodes (4): Authentication, Login, Logout, Middleware

### Community 34 - "UI Components"
Cohesion: 0.40
Nodes (4): Halaman Admin, Halaman Publik, Konvensi Visual, UI Components

### Community 35 - "Validation"
Cohesion: 0.40
Nodes (4): Aturan per Field, Normalisasi, Penanganan Error, Validation

### Community 36 - "Internal Contracts"
Cohesion: 0.40
Nodes (4): Endpoint Internal, Internal Contracts, Respons `scan-process.php`, Service Helper

### Community 37 - "Deployment"
Cohesion: 0.40
Nodes (4): Aplikasi, Deployment, Persiapan Perangkat Keras Hari-H, Uji Coba Sebelum Acara

### Community 38 - "Definition of Done"
Cohesion: 0.40
Nodes (4): Definition of Done, Fungsional, Kualitas, Operasional

### Community 39 - "Goals"
Cohesion: 0.50
Nodes (3): Alur Utama, Goals, Sasaran

## Knowledge Gaps
- **114 isolated node(s):** `0. Cek server`, `1. Update & pasang paket`, `2. Amankan MySQL`, `3. Buat database & user khusus`, `4. Upload file` (+109 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **11 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `db()` connect `functions.php` to `auth.php`?**
  _High betweenness centrality (0.013) - this node is a cross-community bridge._
- **Why does `Instalasi di VPS (Ubuntu 22.04 / 24.04)` connect `Instalasi di VPS (Ubuntu 22.04 / 24.04)` to `COI Ministry — Sistem Registrasi & Check-in Acara`?**
  _High betweenness centrality (0.011) - this node is a cross-community bridge._
- **Why does `COI Ministry — Sistem Registrasi & Check-in Acara` connect `COI Ministry — Sistem Registrasi & Check-in Acara` to `Instalasi di VPS (Ubuntu 22.04 / 24.04)`?**
  _High betweenness centrality (0.008) - this node is a cross-community bridge._
- **Are the 8 inferred relationships involving `db()` (e.g. with `setujui_peserta()` and `tolak_peserta()`) actually correct?**
  _`db()` has 8 INFERRED edges - model-reasoned connections that need verification._
- **What connects `0. Cek server`, `1. Update & pasang paket`, `2. Amankan MySQL` to the rest of the system?**
  _114 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `functions.php` be split into smaller, more focused modules?**
  _Cohesion score 0.10256410256410256 - nodes in this community are weakly interconnected._
- **Should `Panduan Deploy ke VPS` be split into smaller, more focused modules?**
  _Cohesion score 0.10526315789473684 - nodes in this community are weakly interconnected._