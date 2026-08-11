# Graph Report - coiministry  (2026-08-11)

## Corpus Check
- 56 files · ~367,439 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 252 nodes · 246 edges · 51 communities (40 shown, 11 thin omitted)
- Extraction: 90% EXTRACTED · 10% INFERRED · 0% AMBIGUOUS · INFERRED: 25 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `896938f7`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- functions.php
- db
- Panduan Deploy ke VPS
- mailer.php
- require
- Instalasi di VPS (Ubuntu 22.04 / 24.04)
- COI Ministry — Sistem Registrasi & Check-in Acara
- Check-in Flow
- QR Code Generation
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
6. `kirim_qr_peserta()` - 7 edges
7. `kirim_email_ditolak()` - 7 edges
8. `QR Code Generation` - 7 edges
9. `Testing Checklist` - 7 edges
10. `setujui_peserta()` - 6 edges

## Surprising Connections (you probably didn't know these)
- `setujui_peserta()` --calls--> `kirim_qr_peserta()`  [INFERRED]
  admin/approval.php → includes/mailer.php
- `tolak_peserta()` --calls--> `kirim_email_ditolak()`  [INFERRED]
  admin/approval.php → includes/mailer.php
- `admin_login()` --calls--> `db()`  [INFERRED]
  includes/auth.php → config/database.php
- `jumlah_diterima()` --calls--> `db()`  [INFERRED]
  includes/functions.php → config/database.php
- `statistik_peserta()` --calls--> `db()`  [INFERRED]
  includes/functions.php → config/database.php

## Import Cycles
- None detected.

## Communities (51 total, 11 thin omitted)

### Community 0 - "functions.php"
Cohesion: 0.09
Nodes (14): url_hal(), admin_logged_in(), require_admin(), require_admin_json(), csrf_field(), csrf_require(), csrf_token(), csrf_verify() (+6 more)

### Community 1 - "db"
Cohesion: 0.16
Nodes (14): setujui_peserta(), tolak_peserta(), db(), admin_login(), cari_peserta_by_id(), cari_peserta_by_kode(), jumlah_diterima(), normalisasi_kode() (+6 more)

### Community 2 - "Panduan Deploy ke VPS"
Cohesion: 0.11
Nodes (18): 0. Cek server, 10. Pasang SSL (HTTPS), 11. Buat akun admin, 12. Aktifkan email, 13. Uji sebelum sebar link, 14. Backup otomatis, 1. Update & pasang paket, 2. Amankan MySQL (+10 more)

### Community 3 - "mailer.php"
Cohesion: 0.41
Nodes (11): e(), kirim_email_ditolak(), kirim_email_pending(), kirim_polos_phpmailer(), kirim_qr_peserta(), kirim_via_mail(), kirim_via_phpmailer(), muat_phpmailer() (+3 more)

### Community 4 - "require"
Cohesion: 0.50
Nodes (3): require, chillerlan/php-qrcode, phpmailer/phpmailer

### Community 22 - "Instalasi di VPS (Ubuntu 22.04 / 24.04)"
Cohesion: 0.11
Nodes (18): 0. Masuk ke server, 10. Arahkan domain, 11. Pasang SSL (HTTPS) — Gratis dengan Let's Encrypt, 12. Buat akun admin, 13. Verifikasi akhir, 1. Update sistem & pasang semua paket, 2. Amankan MySQL, 3. Buat database & user khusus (+10 more)

### Community 23 - "COI Ministry — Sistem Registrasi & Check-in Acara"
Cohesion: 0.17
Nodes (11): Alur Sistem, Catatan Perangkat Keras, COI Ministry — Sistem Registrasi & Check-in Acara, Instalasi Lokal (Pengembangan), Keamanan, Kebutuhan, Kebutuhan Server, Langkah (+3 more)

### Community 24 - "Check-in Flow"
Cohesion: 0.17
Nodes (11): Alur Lengkap di Lapangan, Aturan Penting, Cadangan, Cara Kerja Scanner QR, Check-in Flow, Logika Validasi Tahap 1, Logika Validasi Tahap 2, Perilaku Field Scan (+3 more)

### Community 25 - "QR Code Generation"
Cohesion: 0.25
Nodes (7): Dua Media QR Code, Format Kode, Keterbacaan dari Layar Ponsel, Penyimpanan, QR Code Generation, Simbologi, Spesifikasi Gambar untuk Email

### Community 26 - "Testing Checklist"
Cohesion: 0.25
Nodes (7): Admin, Cetak Gelang, Pendaftaran, Tahap 1 — Daftar Ulang, Tahap 2 — Masuk Ruangan, Testing Checklist, Umum

### Community 27 - "Participants Module"
Cohesion: 0.29
Nodes (6): Cetak Gelang, Fitur Pendukung, Kirim Ulang QR, Kolom Tabel, Participants Module, Tombol Aksi

### Community 28 - "Wristband Printing"
Cohesion: 0.29
Nodes (6): Alur Cetak, Aturan Penting, Dimensi QR Code di Gelang, Isi Gelang, Spesifikasi Cetak, Wristband Printing

### Community 29 - "Database"
Cohesion: 0.33
Nodes (5): Database, DDL, Dua Tahap Kehadiran, Tabel `admin`, Tabel `peserta`

### Community 30 - "Registration Flow"
Cohesion: 0.33
Nodes (5): Aturan Penting, Field Form, Langkah Pengguna, Registration Flow, Urutan Proses Server

### Community 31 - "Email Service"
Cohesion: 0.33
Nodes (5): Aturan Tampilan QR Code di Email, Email Service, Isi Email, Pemicu Pengiriman, Penanganan Kegagalan

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
- **116 isolated node(s):** `phpmailer/phpmailer`, `chillerlan/php-qrcode`, `0. Cek server`, `1. Update & pasang paket`, `2. Amankan MySQL` (+111 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **11 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Instalasi di VPS (Ubuntu 22.04 / 24.04)` connect `Instalasi di VPS (Ubuntu 22.04 / 24.04)` to `COI Ministry — Sistem Registrasi & Check-in Acara`?**
  _High betweenness centrality (0.011) - this node is a cross-community bridge._
- **Why does `COI Ministry — Sistem Registrasi & Check-in Acara` connect `COI Ministry — Sistem Registrasi & Check-in Acara` to `Instalasi di VPS (Ubuntu 22.04 / 24.04)`?**
  _High betweenness centrality (0.008) - this node is a cross-community bridge._
- **Are the 8 inferred relationships involving `db()` (e.g. with `setujui_peserta()` and `tolak_peserta()`) actually correct?**
  _`db()` has 8 INFERRED edges - model-reasoned connections that need verification._
- **What connects `phpmailer/phpmailer`, `chillerlan/php-qrcode`, `0. Cek server` to the rest of the system?**
  _116 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `functions.php` be split into smaller, more focused modules?**
  _Cohesion score 0.08994708994708994 - nodes in this community are weakly interconnected._
- **Should `Panduan Deploy ke VPS` be split into smaller, more focused modules?**
  _Cohesion score 0.10526315789473684 - nodes in this community are weakly interconnected._
- **Should `Instalasi di VPS (Ubuntu 22.04 / 24.04)` be split into smaller, more focused modules?**
  _Cohesion score 0.1111111111111111 - nodes in this community are weakly interconnected._