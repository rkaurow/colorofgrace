<?php
/**
 * Konfigurasi SMTP untuk pengiriman email.
 *
 * Gmail : aktifkan 2FA lalu buat "App Password" 16 karakter.
 * Brevo : ambil SMTP key dari dashboard Brevo.
 *
 * Bila MAIL_ENABLED = false, email tidak dikirim.
 * Berguna saat pengembangan lokal agar registrasi tetap bisa diuji.
 */

define('MAIL_ENABLED',   false);

define('SMTP_HOST',      'smtp.gmail.com');
define('SMTP_PORT',      587);
define('SMTP_SECURE',    'tls');          // 'tls' atau 'ssl'
define('SMTP_USER',      'emailkamu@gmail.com');
define('SMTP_PASS',      'app-password-16-karakter');

define('MAIL_FROM',      'emailkamu@gmail.com');
define('MAIL_FROM_NAME', 'COI Ministry');
