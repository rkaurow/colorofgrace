<?php
/**
 * Komponen halaman scan kehadiran (satu tahap).
 *
 * Variabel yang harus disiapkan pemanggil:
 *   $tahap        Identitas tahap yang dikirim ke scan-process.php
 *   $judul        Judul halaman
 *   $subjudul     Keterangan singkat
 *   $warna        Warna aksen (hex)
 *   $ikon         Data path SVG (atribut d), viewBox 24x24
 *   $stat_label   Label angka statistik
 *   $stat_nilai   Nilai statistik
 *   $stat_total   Total peserta yang berhak hadir (status_acc = diterima)
 *
 * Catatan: komponen ini di-include SETELAH includes/header.php,
 * sehingga Tailwind + font sudah tersedia.
 */
?>
<main class="mx-auto max-w-[100rem] px-4 py-6 sm:px-6 lg:px-8" style="--aksen: <?= e($warna) ?>;">

  <!-- Kepala halaman -->
  <div class="mb-6 flex flex-wrap items-center justify-between gap-4 border-l-[5px] pl-4"
       style="border-color: var(--aksen);">
    <div>
      <h1 class="flex items-center gap-2.5 font-display text-2xl font-black text-slate-900">
        <svg class="h-7 w-7 shrink-0" style="color: var(--aksen);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="<?= e($ikon) ?>" />
        </svg>
        <?= e($judul) ?>
      </h1>
      <p class="mt-1 text-sm text-slate-500"><?= e($subjudul) ?></p>
    </div>
    <div class="text-right">
      <div class="text-xs font-bold uppercase tracking-wider text-slate-400"><?= e($stat_label) ?></div>
      <div class="text-3xl font-black leading-tight" style="color: var(--aksen);">
        <span id="statNilai"><?= number_format($stat_nilai, 0, ',', '.') ?></span><span
          class="text-lg font-bold text-slate-300"> / <?= number_format($stat_total, 0, ',', '.') ?></span>
      </div>
    </div>
  </div>

  <div class="grid gap-6 lg:grid-cols-12">

    <!-- ============ Kolom kiri: input scan ============ -->
    <div class="lg:col-span-5">
      <div class="rounded-2xl border border-slate-200 border-t-4 bg-white p-6 shadow-sm"
           style="border-top-color: var(--aksen);">

        <label for="inputKode" class="mb-2 flex items-center gap-2 text-sm font-bold text-slate-700">
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875v14.25m3-14.25v14.25m3.75-14.25v14.25m3-14.25v14.25m3.75-14.25v14.25m3-14.25v14.25" />
          </svg>
          Arahkan scanner ke QR Code
        </label>

        <input type="text" id="inputKode"
               class="scan-input w-full rounded-xl px-4 py-3 text-center outline-none"
               placeholder="Menunggu scan..." autocomplete="off"
               autocapitalize="off" spellcheck="false">

        <p class="mt-2 flex items-start gap-1.5 text-xs text-slate-400">
          <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
          </svg>
          Kolom ini harus selalu aktif. Jangan klik di area lain saat memindai.
        </p>

        <div id="statusFokus" role="alert"
             class="mt-3 hidden items-start gap-2 rounded-xl border-2 border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
          <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
          </svg>
          <span>Kolom scan tidak aktif.
            <a href="#" id="linkFokus" class="font-bold underline">Klik di sini</a> untuk mengaktifkan.</span>
        </div>

        <hr class="my-6 border-slate-100">

        <h2 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-400">Cadangan</h2>

        <label for="cariNama" class="mb-1.5 block text-xs font-semibold text-slate-500">
          Cari berdasarkan nama
        </label>
        <div class="relative">
          <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
          </span>
          <input type="text" id="cariNama" placeholder="Ketik nama peserta..."
                 class="w-full rounded-xl border-2 border-slate-200 py-2.5 pl-12 pr-4 text-sm outline-none transition focus:border-coi-500 focus:ring-2 focus:ring-coi-100">
        </div>
        <div id="hasilCari" class="mt-2 overflow-hidden rounded-xl empty:hidden"></div>

      </div>
    </div>

    <!-- ============ Kolom kanan: hasil scan ============ -->
    <div class="lg:col-span-7">
      <div class="flex h-full flex-col rounded-2xl border border-slate-200 border-t-4 bg-white p-6 shadow-sm"
           style="border-top-color: var(--aksen);">

        <div id="panelKosong" class="my-auto py-12 text-center text-slate-400">
          <svg class="mx-auto mb-4 h-20 w-20 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="<?= e($ikon) ?>" />
          </svg>
          <p class="text-sm">Hasil scan akan muncul di sini.</p>
        </div>

        <div id="panelHasil" class="hidden">
          <div id="hasilBox" class="hasil-box mb-4 p-6 text-center">
            <div id="hasilIkon" class="hasil-ikon mx-auto mb-3 flex justify-center"></div>
            <div id="hasilNama" class="font-display text-3xl font-black leading-tight text-slate-900"></div>
            <div id="hasilGereja" class="mt-1 text-sm text-slate-500"></div>
            <div id="hasilKode" class="mt-2"></div>
            <div id="hasilPesan" class="mt-2 font-bold"></div>
            <div id="hasilWaktu" class="mt-1 text-xs text-slate-500"></div>
          </div>
        </div>

        <!-- Riwayat scan sesi ini -->
        <div class="mt-6">
          <h2 class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-400">
            Riwayat Scan (<span id="jumlahRiwayat">0</span>)
          </h2>
          <div id="riwayat" class="max-h-[230px] overflow-y-auto text-sm">
            <div class="py-2 text-slate-400">Belum ada scan.</div>
          </div>
        </div>

      </div>
    </div>

  </div>
</main>

<style>
  .scan-input {
    font-size: 1.5rem; font-weight: 800; letter-spacing: 3px;
    border: 3px dashed #cbd5e1; transition: border-color .15s, box-shadow .15s;
  }
  .scan-input:focus {
    border-style: solid; border-color: var(--aksen);
    box-shadow: 0 0 0 4px rgb(0 0 0 / .06);
  }
  #statusFokus:not(.hidden) { display: flex; }

  .hasil-box { border-radius: 1rem; border: 2px solid transparent; transition: background .2s, border-color .2s; }
  .hasil-box.ok    { background: #ccfbf1; border-color: #0d9488; }
  .hasil-box.warn  { background: #fef3c7; border-color: #d97706; }
  .hasil-box.error { background: #fee2e2; border-color: #b91c1c; }
  .hasil-box.ok    .hasil-ikon { color: #0d9488; }
  .hasil-box.warn  .hasil-ikon { color: #b45309; }
  .hasil-box.error .hasil-ikon { color: #b91c1c; }
  .hasil-box.ok    #hasilPesan { color: #115e59; }
  .hasil-box.warn  #hasilPesan { color: #92400e; }
  .hasil-box.error #hasilPesan { color: #991b1b; }

  .riwayat-item {
    display: flex; justify-content: space-between; gap: 10px;
    padding: .5rem .25rem; border-bottom: 1px solid #f1f5f9;
  }

  @keyframes pop { 0% { transform: scale(.94); opacity: .4 } 100% { transform: scale(1); opacity: 1 } }
  .pop { animation: pop .18s ease-out; }
  @media (prefers-reduced-motion: reduce) {
    .pop { animation: none; }
  }
</style>

<script>
(function () {
  const TAHAP = <?= json_encode($tahap) ?>;
  const CSRF  = <?= json_encode(csrf_token()) ?>;
  const URL_SCAN = <?= json_encode(BASE_URL . '/admin/scan-process.php') ?>;
  const URL_CARI = <?= json_encode(BASE_URL . '/admin/cari-peserta.php') ?>;

  const inp        = document.getElementById('inputKode');
  const cariNama   = document.getElementById('cariNama');
  const hasilCari  = document.getElementById('hasilCari');
  const panelKosong= document.getElementById('panelKosong');
  const panelHasil = document.getElementById('panelHasil');
  const box        = document.getElementById('hasilBox');
  const statusFokus= document.getElementById('statusFokus');

  // Path SVG hasil scan (viewBox 24x24)
  const IKON = {
    ok:    'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
    warn:  'M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z',
    stop:  'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z',
    error: 'm9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',

  };
  let sedangProses = false;
  let kodeTerakhir = '';
  let waktuTerakhir= 0;
  const riwayat    = [];

  // ---------- Audio feedback ----------
  let actx = null;
  function bunyi(tipe) {
    try {
      actx = actx || new (window.AudioContext || window.webkitAudioContext)();
      const pola = {
        ok:    [[880, 0, .10], [1320, .10, .14]],
        warn:  [[600, 0, .18]],
        error: [[300, 0, .16], [220, .18, .22]],
      }[tipe] || [];
      pola.forEach(([freq, mulai, durasi]) => {
        const osc = actx.createOscillator();
        const gain= actx.createGain();
        osc.connect(gain); gain.connect(actx.destination);
        osc.frequency.value = freq;
        osc.type = 'sine';
        const t = actx.currentTime + mulai;
        gain.gain.setValueAtTime(.0001, t);
        gain.gain.exponentialRampToValueAtTime(.35, t + .01);
        gain.gain.exponentialRampToValueAtTime(.0001, t + durasi);
        osc.start(t); osc.stop(t + durasi + .02);
      });
    } catch (e) { /* audio tidak wajib */ }
  }

  // ---------- Jaga fokus tetap di kolom scan ----------
  function fokus() { inp.focus({ preventScroll: true }); }

  function cekFokus() {
    const aktif = document.activeElement;
    const bolehLain = aktif === cariNama || (aktif && aktif.closest('#hasilCari'));
    statusFokus.classList.toggle('hidden', aktif === inp || bolehLain);
  }

  document.addEventListener('click', (ev) => {
    // Biarkan pengguna memakai pencarian nama & tombol tanpa direbut fokusnya
    if (ev.target.closest('#cariNama, #hasilCari, button, a, input')) { setTimeout(cekFokus, 50); return; }
    fokus(); cekFokus();
  });
  document.getElementById('linkFokus').addEventListener('click', (ev) => { ev.preventDefault(); fokus(); cekFokus(); });
  inp.addEventListener('blur', () => setTimeout(cekFokus, 60));
  inp.addEventListener('focus', cekFokus);
  window.addEventListener('focus', fokus);
  setInterval(cekFokus, 1500);
  fokus();

  // ---------- Scanner menekan Enter setelah mengirim kode ----------
  inp.addEventListener('keydown', (ev) => {
    if (ev.key !== 'Enter') return;
    ev.preventDefault();
    const kode = inp.value.trim();
    inp.value = '';
    if (!kode) return;

    // Debounce: abaikan kode sama dalam 1,5 detik (scanner kadang memicu ganda)
    const now = Date.now();
    if (kode === kodeTerakhir && (now - waktuTerakhir) < 1500) return;
    kodeTerakhir = kode; waktuTerakhir = now;

    kirimScan(kode);
  });

  // ---------- Kirim ke server ----------
  async function kirimScan(kode) {
    if (sedangProses) return;
    sedangProses = true;

    try {
      const body = new URLSearchParams({ kode, tahap: TAHAP, csrf_token: CSRF });
      const res  = await fetch(URL_SCAN, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body
      });

      if (res.status === 401) {
        tampilkan({ status:'invalid', message:'Sesi berakhir. Silakan login ulang.', kode });
        setTimeout(() => location.href = <?= json_encode(BASE_URL . '/admin/login.php') ?>, 1500);
        return;
      }

      const data = await res.json();
      tampilkan(data);
    } catch (err) {
      tampilkan({ status:'invalid', message:'Gagal terhubung ke server. Periksa koneksi.', kode });
    } finally {
      sedangProses = false;
      fokus();
    }
  }

  // ---------- Render hasil ----------
  function tampilkan(d) {
    const gaya = {
      success:         { cls:'ok',    ikon:IKON.ok,    suara:'ok'    },
      duplicate:       { cls:'warn',  ikon:IKON.warn,  suara:'warn'  },
      belum_disetujui: { cls:'error', ikon:IKON.stop,  suara:'error' },
      invalid:         { cls:'error', ikon:IKON.error, suara:'error' },
    }[d.status] || { cls:'error', ikon:IKON.error, suara:'error' };

    panelKosong.classList.add('hidden');
    panelHasil.classList.remove('hidden');

    box.className = 'hasil-box mb-4 p-6 text-center pop ' + gaya.cls;
    void box.offsetWidth; // paksa animasi berulang

    document.getElementById('hasilIkon').innerHTML =
      '<svg class="h-14 w-14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">' +
      '<path stroke-linecap="round" stroke-linejoin="round" d="' + gaya.ikon + '" /></svg>';

    document.getElementById('hasilNama').textContent   = d.nama || 'Tidak Dikenali';
    document.getElementById('hasilGereja').textContent = d.gereja || '';
    document.getElementById('hasilKode').innerHTML     = d.kode
      ? '<code class="rounded-md bg-white/60 px-2 py-1 font-mono text-sm text-slate-700">' + esc(d.kode) + '</code>'
      : '';
    document.getElementById('hasilPesan').textContent  = d.message || '';
    document.getElementById('hasilWaktu').textContent  = d.waktu ? ('Waktu: ' + d.waktu) : '';

    bunyi(gaya.suara);

    if (d.status === 'success') naikkanStat();
    tambahRiwayat(d, gaya.cls);
  }

  function naikkanStat() {
    const el = document.getElementById('statNilai');
    const n  = parseInt(el.textContent.replace(/\D/g, ''), 10) || 0;
    el.textContent = (n + 1).toLocaleString('id-ID');
  }

  function tambahRiwayat(d, cls) {
    const warna = { ok:'text-emerald-600', warn:'text-amber-600', error:'text-red-600' }[cls];
    riwayat.unshift({
      nama: d.nama || d.kode || '—',
      pesan: d.message || '',
      jam: new Date().toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit', second:'2-digit' }),
      warna
    });
    if (riwayat.length > 50) riwayat.pop();

    document.getElementById('jumlahRiwayat').textContent = riwayat.length;
    document.getElementById('riwayat').innerHTML = riwayat.map(r =>
      '<div class="riwayat-item">' +
        '<div class="truncate"><span class="font-semibold text-slate-700">' + esc(r.nama) + '</span>' +
        '<span class="' + r.warna + ' ml-2">' + esc(r.pesan) + '</span></div>' +
        '<div class="shrink-0 text-slate-400">' + r.jam + '</div>' +
      '</div>'
    ).join('');
  }

  // ---------- Pencarian nama (cadangan) ----------
  let timerCari = null;
  cariNama.addEventListener('input', () => {
    clearTimeout(timerCari);
    const q = cariNama.value.trim();
    if (q.length < 2) { hasilCari.innerHTML = ''; return; }
    timerCari = setTimeout(() => jalankanCari(q), 300);
  });

  async function jalankanCari(q) {
    try {
      const res  = await fetch(URL_CARI + '?q=' + encodeURIComponent(q));
      const data = await res.json();

      if (!data.length) {
        hasilCari.innerHTML =
          '<div class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-400">Tidak ditemukan.</div>';
        return;
      }

      hasilCari.innerHTML =
        '<div class="divide-y divide-slate-100 rounded-xl border border-slate-200">' +
        data.map(p =>
          '<button type="button" data-kode="' + esc(p.kode) + '" ' +
            'class="block w-full px-3 py-2 text-left transition hover:bg-slate-50">' +
            '<div class="text-sm font-semibold text-slate-800">' + esc(p.nama) + '</div>' +
            '<div class="text-xs text-slate-400">' + esc(p.gereja) + ' &bull; ' + esc(p.kode) + '</div>' +
          '</button>'
        ).join('') +
        '</div>';

      hasilCari.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('click', () => {
          kirimScan(btn.dataset.kode);
          cariNama.value = '';
          hasilCari.innerHTML = '';
          fokus();
        });
      });
    } catch (e) {
      hasilCari.innerHTML =
        '<div class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-600">Gagal memuat data.</div>';
    }
  }

  function esc(s) {
    const d = document.createElement('div');
    d.textContent = s == null ? '' : String(s);
    return d.innerHTML;
  }
})();
</script>
