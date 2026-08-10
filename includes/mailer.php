<?php
/**
 * Pengiriman email berisi barcode peserta.
 *
 * Memakai PHPMailer bila tersedia di /vendor, jika tidak akan
 * fallback ke fungsi mail() bawaan PHP.
 */

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/barcode.php';
require_once __DIR__ . '/../config/smtp.php';

/** Muat PHPMailer bila ada. */
function muat_phpmailer(): bool
{
    if (class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
        return true;
    }

    $autoload = BASE_PATH . '/vendor/autoload.php';
    if (is_file($autoload)) {
        require_once $autoload;
        return class_exists(\PHPMailer\PHPMailer\PHPMailer::class);
    }

    $manual = BASE_PATH . '/vendor/PHPMailer/src/PHPMailer.php';
    if (is_file($manual)) {
        require_once BASE_PATH . '/vendor/PHPMailer/src/Exception.php';
        require_once BASE_PATH . '/vendor/PHPMailer/src/PHPMailer.php';
        require_once BASE_PATH . '/vendor/PHPMailer/src/SMTP.php';
        return class_exists(\PHPMailer\PHPMailer\PHPMailer::class);
    }

    return false;
}

/**
 * Template email HTML.
 */
function template_email_barcode(array $peserta): string
{
    $nama = e($peserta['nama']);
    $kode = e($peserta['kode']);

    $nama_acara    = e(EVENT_NAME);
    $tanggal_acara = e(EVENT_DATE_TEXT);
    $jam_acara     = e(EVENT_TIME);
    $lokasi_acara  = e(EVENT_LOCATION) . ' — ' . e(EVENT_ADDRESS);

    return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,Helvetica,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:24px 12px;">
    <tr><td align="center">
      <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);">

        <tr>
          <td style="background:linear-gradient(135deg,#134e4a,#0d9488);background-color:#0d9488;padding:28px 24px;text-align:center;">
            <h1 style="margin:0;color:#ffffff;font-size:24px;letter-spacing:.5px;">Pendaftaran Diterima!</h1>
            <p style="margin:6px 0 0;color:#ffedd5;font-size:14px;">{$nama_acara}</p>
          </td>
        </tr>

        <tr>
          <td style="padding:28px 24px;">
            <p style="margin:0 0 14px;font-size:15px;color:#212529;">Halo <strong>{$nama}</strong>,</p>
            <p style="margin:0 0 20px;font-size:14px;color:#495057;line-height:1.6;">
              Selamat! Pendaftaranmu telah <strong>disetujui</strong>. Tunjukkan barcode di bawah ini
              kepada petugas saat datang pada 22 Agustus 2026.
            </p>

            <div style="text-align:center;background:#ffffff;border:1px solid #dee2e6;border-radius:10px;padding:20px;margin-bottom:20px;">
              <img src="cid:barcode_peserta" alt="Barcode {$kode}" style="max-width:100%;height:auto;display:block;margin:0 auto;">
              <p style="margin:12px 0 0;font-size:18px;font-weight:bold;letter-spacing:2px;color:#212529;">{$kode}</p>
            </div>

            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;color:#212529;border-collapse:collapse;">
              <tr><td style="padding:8px 0;border-bottom:1px solid #f1f3f5;width:38%;color:#6c757d;">Acara</td><td style="padding:8px 0;border-bottom:1px solid #f1f3f5;">{$nama_acara}</td></tr>
              <tr><td style="padding:8px 0;border-bottom:1px solid #f1f3f5;color:#6c757d;">Tanggal</td><td style="padding:8px 0;border-bottom:1px solid #f1f3f5;">{$tanggal_acara}</td></tr>
              <tr><td style="padding:8px 0;border-bottom:1px solid #f1f3f5;color:#6c757d;">Waktu</td><td style="padding:8px 0;border-bottom:1px solid #f1f3f5;">{$jam_acara}</td></tr>
              <tr><td style="padding:8px 0;color:#6c757d;">Lokasi</td><td style="padding:8px 0;">{$lokasi_acara}</td></tr>
            </table>

            <div style="margin-top:22px;background:#fff8e1;border-left:4px solid #ffc107;padding:12px 14px;border-radius:4px;">
              <p style="margin:0;font-size:13px;color:#664d03;line-height:1.6;">
                <strong>Penting:</strong> Simpan email ini. Barcode wajib ditunjukkan saat datang.
                Cukup pindai barcode ini satu kali di pintu masuk acara.
              </p>
            </div>
          </td>
        </tr>

        <tr>
          <td style="background:#f8f9fa;padding:18px 24px;text-align:center;border-top:1px solid #e9ecef;">
            <p style="margin:0;font-size:12px;color:#6c757d;">
              Email ini dikirim otomatis, mohon tidak membalas.
            </p>
          </td>
        </tr>

      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
}

/**
 * Kirim email barcode ke peserta.
 *
 * @return bool true bila terkirim.
 */
function kirim_barcode_peserta(array $peserta): bool
{
    if (!MAIL_ENABLED) {
        error_log('MAIL_ENABLED = false, email ke ' . $peserta['email'] . ' dilewati.');
        return false;
    }

    $kode = $peserta['kode'];
    $file = buat_barcode_peserta($kode);

    if ($file === null) {
        error_log('Gagal membuat barcode untuk ' . $kode);
        return false;
    }

    $subjek = 'Barcode Pendaftaran - ' . EVENT_NAME;
    $html   = template_email_barcode($peserta);

    if (muat_phpmailer()) {
        return kirim_via_phpmailer($peserta, $subjek, $html, $file);
    }

    return kirim_via_mail($peserta, $subjek, $html);
}

/** Pengiriman via PHPMailer + SMTP (disarankan). */
function kirim_via_phpmailer(array $peserta, string $subjek, string $html, string $fileBarcode): bool
{
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($peserta['email'], $peserta['nama']);

        // Barcode disematkan sebagai inline image (cid)
        $mail->addEmbeddedImage($fileBarcode, 'barcode_peserta', basename($fileBarcode));

        $mail->isHTML(true);
        $mail->Subject = $subjek;
        $mail->Body    = $html;
        $mail->AltBody = 'Kode pendaftaran kamu: ' . $peserta['kode']
            . '. Tunjukkan barcode saat datang di lokasi acara.';

        $mail->send();
        return true;
    } catch (\Throwable $e) {
        error_log('PHPMailer gagal: ' . $e->getMessage());
        return false;
    }
}

/** Fallback sederhana memakai mail() bawaan PHP. */
function kirim_via_mail(array $peserta, string $subjek, string $html): bool
{
    // Tanpa PHPMailer, barcode dikirim sebagai tautan gambar
    $urlBarcode = url_barcode_peserta($peserta['kode']);
    $html = str_replace('cid:barcode_peserta', $urlBarcode, $html);

    $headers = implode("\r\n", [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM . '>',
    ]);

    $ok = @mail($peserta['email'], $subjek, $html, $headers);

    if (!$ok) {
        error_log('mail() gagal mengirim ke ' . $peserta['email']);
    }

    return $ok;
}

/** Tandai waktu pengiriman email peserta. */
function tandai_email_terkirim(int $pesertaId): void
{
    db()->prepare('UPDATE peserta SET email_sent_at = NOW() WHERE id = ?')
        ->execute([$pesertaId]);
}

// =========================================================
// Email tanpa barcode (pending & ditolak)
// =========================================================

/** Kerangka email sederhana yang dipakai template pending & ditolak. */
function template_email_polos(string $nama, string $judul, string $warna, string $isi): string
{
    $nama_acara    = e(EVENT_NAME);
    $tanggal_acara = e(EVENT_DATE_TEXT);
    $jam_acara     = e(EVENT_TIME);
    $lokasi_acara  = e(EVENT_LOCATION) . ' — ' . e(EVENT_ADDRESS);
    $namaAman      = e($nama);
    $judulAman     = e($judul);

    return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,Helvetica,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:24px 12px;">
    <tr><td align="center">
      <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);">

        <tr>
          <td style="background:{$warna};padding:28px 24px;text-align:center;">
            <h1 style="margin:0;color:#ffffff;font-size:22px;">{$judulAman}</h1>
            <p style="margin:6px 0 0;color:#ffffff;opacity:.85;font-size:14px;">{$nama_acara}</p>
          </td>
        </tr>

        <tr>
          <td style="padding:28px 24px;">
            <p style="margin:0 0 14px;font-size:15px;color:#212529;">Halo <strong>{$namaAman}</strong>,</p>
            {$isi}

            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:22px;font-size:14px;color:#212529;border-collapse:collapse;">
              <tr><td style="padding:8px 0;border-bottom:1px solid #f1f3f5;width:38%;color:#6c757d;">Acara</td><td style="padding:8px 0;border-bottom:1px solid #f1f3f5;">{$nama_acara}</td></tr>
              <tr><td style="padding:8px 0;border-bottom:1px solid #f1f3f5;color:#6c757d;">Tanggal</td><td style="padding:8px 0;border-bottom:1px solid #f1f3f5;">{$tanggal_acara}</td></tr>
              <tr><td style="padding:8px 0;border-bottom:1px solid #f1f3f5;color:#6c757d;">Waktu</td><td style="padding:8px 0;border-bottom:1px solid #f1f3f5;">{$jam_acara}</td></tr>
              <tr><td style="padding:8px 0;color:#6c757d;">Lokasi</td><td style="padding:8px 0;">{$lokasi_acara}</td></tr>
            </table>
          </td>
        </tr>

        <tr>
          <td style="background:#f8f9fa;padding:18px 24px;text-align:center;border-top:1px solid #e9ecef;">
            <p style="margin:0;font-size:12px;color:#6c757d;">Email ini dikirim otomatis, mohon tidak membalas.</p>
          </td>
        </tr>

      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
}

/** Email konfirmasi bahwa pendaftaran sedang ditinjau. */
function kirim_email_pending(array $peserta): bool
{
    if (!MAIL_ENABLED) {
        return false;
    }

    $isi = '<p style="margin:0 0 16px;font-size:14px;color:#495057;line-height:1.6;">
              Terima kasih sudah mendaftar. Pendaftaranmu sudah kami terima dan
              sedang <strong>menunggu konfirmasi panitia</strong>.
            </p>
            <div style="background:#fff8e1;border-left:4px solid #ffc107;padding:12px 14px;border-radius:4px;">
              <p style="margin:0;font-size:13px;color:#664d03;line-height:1.6;">
                Panitia akan meninjau pendaftaran terlebih dahulu.
                Bila disetujui, kamu akan menerima email berisi <strong>barcode tiket</strong>
                yang wajib ditunjukkan saat datang.
              </p>
            </div>';

    $html   = template_email_polos($peserta['nama'], 'Pendaftaran Diterima', '#0d9488', $isi);
    $subjek = 'Pendaftaran Diterima - ' . EVENT_NAME;

    if (muat_phpmailer()) {
        return kirim_polos_phpmailer($peserta, $subjek, $html);
    }
    return kirim_via_mail($peserta, $subjek, $html);
}

/** Email pemberitahuan bahwa pendaftaran tidak dapat dilanjutkan. */
function kirim_email_ditolak(array $peserta, string $catatan = ''): bool
{
    if (!MAIL_ENABLED) {
        return false;
    }

    $tambahan = $catatan !== ''
        ? '<div style="margin-top:14px;background:#f8f9fa;border-left:4px solid #adb5bd;padding:12px 14px;border-radius:4px;">
             <p style="margin:0;font-size:13px;color:#495057;line-height:1.6;">' . e($catatan) . '</p>
           </div>'
        : '';

    $isi = '<p style="margin:0 0 16px;font-size:14px;color:#495057;line-height:1.6;">
              Terima kasih atas antusiasmemu mendaftar. Mohon maaf, setelah
              peninjauan panitia, pendaftaranmu belum dapat kami setujui kali ini.
            </p>
            <p style="margin:0;font-size:14px;color:#495057;line-height:1.6;">
              Kami sangat berharap dapat bertemu denganmu di acara berikutnya.
              Tuhan memberkati.
            </p>' . $tambahan;

    $html   = template_email_polos($peserta['nama'], 'Informasi Pendaftaran', '#6c757d', $isi);
    $subjek = 'Informasi Pendaftaran - ' . EVENT_NAME;

    if (muat_phpmailer()) {
        return kirim_polos_phpmailer($peserta, $subjek, $html);
    }
    return kirim_via_mail($peserta, $subjek, $html);
}

/** Kirim email tanpa lampiran via PHPMailer. */
function kirim_polos_phpmailer(array $peserta, string $subjek, string $html): bool
{
    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($peserta['email'], $peserta['nama']);
        $mail->isHTML(true);
        $mail->Subject = $subjek;
        $mail->Body    = $html;
        $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], "\n", $html));

        return $mail->send();
    } catch (\Throwable $ex) {
        error_log('PHPMailer gagal: ' . $ex->getMessage());
        return false;
    }
}
