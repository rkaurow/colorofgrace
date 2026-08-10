<?php
require_once __DIR__ . '/includes/functions.php';

$hari_lagi = (int) floor((strtotime(EVENT_DATE) - strtotime(date('Y-m-d'))) / 86400);
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e(EVENT_NAME) ?> — <?= e(EVENT_SUBTITLE) ?></title>
<meta name="description" content="<?= e(EVENT_NAME) ?> — <?= e(EVENT_TAGLINE) ?>. <?= e(EVENT_DATE_TEXT) ?> di <?= e(EVENT_LOCATION) ?>.">

<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        orange: {
          primary: '#F97316',
          light:   '#F59E0B',
          dark:    '#D94B12',
        },
        red: {
          primary: '#C91F25',
          dark:    '#8E151B',
        },
        cream:  '#FFF4D6',
        'cream-light': '#FFF9ED',
        gold:   '#D9A441',
        'gold-light': '#F4C96B',
        navy:   '#07152D',
        'navy-light': '#102442',
      },
      fontFamily: {
        display: ['Cinzel','serif'],
        sans:    ['"Plus Jakarta Sans"','system-ui','sans-serif'],
      },
      animation: {
        'fade-up':   'fadeUp .7s ease-out both',
        'fade-in':   'fadeIn .8s ease-out both',
        'float':     'float 4s ease-in-out infinite',
        'float-slow':'float 6s ease-in-out infinite',
        'confetti':  'confettiFall 8s linear infinite',
      },
      keyframes: {
        fadeUp:      { '0%':{opacity:0,transform:'translateY(24px)'},'100%':{opacity:1,transform:'translateY(0)'} },
        fadeIn:      { '0%':{opacity:0,transform:'scale(.96)'},'100%':{opacity:1,transform:'scale(1)'} },
        float:       { '0%,100%':{transform:'translateY(0)'},'50%':{transform:'translateY(-12px)'} },
        confettiFall:{ '0%':{transform:'translateY(-20px) rotate(0deg)',opacity:1},'100%':{transform:'translateY(110vh) rotate(720deg)',opacity:0} },
      },
    },
  },
};
</script>

<style>
  :root {
    --orange-primary: #F97316;
    --orange-light:   #F59E0B;
    --orange-dark:    #D94B12;
    --red-primary:    #C91F25;
    --red-dark:       #8E151B;
    --cream:          #FFF4D6;
    --cream-light:    #FFF9ED;
    --gold:           #D9A441;
    --gold-light:     #F4C96B;
    --navy:           #07152D;
    --navy-light:     #102442;
  }

  body { background: var(--cream-light); }

  /* ── Hero background ── */
  .hero-bg {
    background:
      radial-gradient(ellipse 70% 60% at 80% 40%, rgba(249,115,22,.28) 0%, transparent 65%),
      radial-gradient(ellipse 50% 50% at 20% 70%, rgba(201,31,37,.18) 0%, transparent 60%),
      radial-gradient(ellipse 80% 80% at 50% 50%, rgba(7,21,45,.0) 0%, transparent 100%),
      linear-gradient(160deg, #0d1b35 0%, #07152D 45%, #1a0a0a 100%);
  }

  /* Light rays */
  .hero-rays {
    background-image:
      repeating-conic-gradient(from 0deg at 75% 50%,
        rgba(249,115,22,.06) 0deg 4deg,
        transparent 4deg 12deg);
  }

  /* Grain texture */
  .hero-grain::after {
    content:'';
    position:absolute;
    inset:0;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
    pointer-events:none;
    opacity:.4;
  }

  /* Glow behind artwork */
  .artwork-glow {
    filter: drop-shadow(0 0 60px rgba(249,115,22,.35)) drop-shadow(0 0 120px rgba(201,31,37,.2));
  }

  /* CTA gradient */
  .btn-cta {
    background: linear-gradient(135deg, #F97316, #E84716);
    box-shadow: 0 8px 32px rgba(249,115,22,.45);
  }
  .btn-cta:hover {
    background: linear-gradient(135deg, #fb923c, #F97316);
    box-shadow: 0 12px 40px rgba(249,115,22,.55);
    transform: translateY(-2px);
  }
  .btn-cta:active { transform: translateY(0); }

  /* Input focus */
  .field-input:focus {
    border-color: var(--orange-primary);
    box-shadow: 0 0 0 3px rgba(249,115,22,.18);
    outline: none;
  }
  .field-input.error { border-color: #ef4444; }
  .field-input.error:focus { box-shadow: 0 0 0 3px rgba(239,68,68,.18); }

  /* Section fade-up on scroll */
  .reveal { opacity:0; transform:translateY(28px); transition: opacity .7s ease, transform .7s ease; }
  .reveal.visible { opacity:1; transform:translateY(0); }

  /* Nav link hover */
  .nav-link { position:relative; }
  .nav-link::after {
    content:''; position:absolute; bottom:-2px; left:0; width:0; height:2px;
    background:var(--orange-primary); transition:width .25s ease;
  }
  .nav-link:hover::after { width:100%; }

  /* Mobile menu */
  #mobile-menu { transition: transform .3s ease, opacity .3s ease; }
  #mobile-menu.hidden { transform:translateY(-8px); opacity:0; pointer-events:none; }

  @media (max-width: 640px) {
    .artwork-glow {
      filter: drop-shadow(0 0 32px rgba(249,115,22,.28)) drop-shadow(0 0 64px rgba(201,31,37,.16));
    }
  }

  @media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { animation:none !important; transition:none !important; }
    .reveal { opacity:1; transform:none; }
  }
</style>
</head>

<body class="overflow-x-hidden font-sans text-slate-800 antialiased">

<!-- ============================================================ -->
<!-- HEADER                                                        -->
<!-- ============================================================ -->
<header id="site-header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300" style="background:transparent">
  <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8">

    <!-- Logo OTW -->
    <a href="<?= e(BASE_URL) ?>/index.php" class="flex-shrink-0">
      <img src="<?= e(BASE_URL) ?>/assets/img/logo-otw.png" alt="One Truth Way" class="h-10 w-auto object-contain sm:h-12">
    </a>

    <!-- Desktop Nav -->
    <nav class="hidden items-center gap-7 lg:flex" aria-label="Navigasi utama">
      <a href="#beranda"  class="nav-link text-sm font-semibold text-white/90 transition hover:text-white">Beranda</a>
      <a href="#tentang"  class="nav-link text-sm font-semibold text-white/90 transition hover:text-white">Tentang Acara</a>
      <a href="#informasi" class="nav-link text-sm font-semibold text-white/90 transition hover:text-white">Informasi</a>
      <a href="#daftar"   class="nav-link text-sm font-semibold text-white/90 transition hover:text-white">Galeri</a>
      <a href="#kontak"   class="nav-link text-sm font-semibold text-white/90 transition hover:text-white">Kontak</a>
    </nav>

    <!-- Desktop CTA -->
    <a href="<?= e(BASE_URL) ?>/register.php"
       class="btn-cta hidden rounded-xl px-5 py-2.5 text-sm font-bold text-white transition lg:inline-flex items-center gap-2">
      Daftar Sekarang
      <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
    </a>

    <!-- Mobile hamburger -->
    <button id="menu-toggle" class="flex h-10 w-10 items-center justify-center rounded-xl text-white lg:hidden" aria-label="Buka menu">
      <svg id="icon-open"  class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
      <svg id="icon-close" class="h-6 w-6 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
  </div>

  <!-- Mobile menu drawer -->
  <div id="mobile-menu" class="hidden lg:hidden" style="background:rgba(7,21,45,.97);backdrop-filter:blur(12px)">
    <nav class="flex flex-col gap-1 px-5 pb-6 pt-2">
      <a href="#beranda"   class="rounded-lg px-4 py-3 text-sm font-semibold text-white/90 transition hover:bg-white/10 hover:text-white">Beranda</a>
      <a href="#tentang"   class="rounded-lg px-4 py-3 text-sm font-semibold text-white/90 transition hover:bg-white/10 hover:text-white">Tentang Acara</a>
      <a href="#informasi" class="rounded-lg px-4 py-3 text-sm font-semibold text-white/90 transition hover:bg-white/10 hover:text-white">Informasi</a>
      <a href="#daftar"    class="rounded-lg px-4 py-3 text-sm font-semibold text-white/90 transition hover:bg-white/10 hover:text-white">Galeri</a>
      <a href="#kontak"    class="rounded-lg px-4 py-3 text-sm font-semibold text-white/90 transition hover:bg-white/10 hover:text-white">Kontak</a>
      <a href="<?= e(BASE_URL) ?>/register.php"
         class="btn-cta mt-3 rounded-xl px-5 py-3 text-center text-sm font-bold text-white">
        Daftar Sekarang →
      </a>
    </nav>
  </div>
</header>

<!-- ============================================================ -->
<!-- HERO                                                          -->
<!-- ============================================================ -->
<section id="beranda" class="hero-bg hero-grain relative min-h-screen overflow-hidden pt-20">

  <!-- Light rays overlay -->
  <div class="hero-rays absolute inset-0 opacity-60 pointer-events-none"></div>

  <!-- Decorative curved shapes -->
  <div class="absolute -bottom-24 -left-24 h-96 w-96 rounded-full opacity-10 pointer-events-none" style="background:radial-gradient(circle,#F97316,transparent 70%)"></div>
  <div class="absolute top-1/3 -right-32 h-80 w-80 rounded-full opacity-15 pointer-events-none" style="background:radial-gradient(circle,#C91F25,transparent 70%)"></div>

  <!-- Confetti particles -->
  <?php
  $confetti_colors = ['#F97316','#F59E0B','#C91F25','#D9A441','#F4C96B','#ffffff'];
  $confetti_shapes = ['rounded-full','rounded-sm','rounded'];
  for ($i = 0; $i < 22; $i++):
    $left  = rand(2, 98);
    $delay = round(rand(0, 7000) / 1000, 1);
    $dur   = round(rand(6000, 12000) / 1000, 1);
    $size  = rand(4, 10);
    $color = $confetti_colors[array_rand($confetti_colors)];
    $shape = $confetti_shapes[array_rand($confetti_shapes)];
  ?>
  <div class="absolute <?= $shape ?> pointer-events-none opacity-70"
       style="left:<?= $left ?>%;top:-20px;width:<?= $size ?>px;height:<?= $size ?>px;background:<?= $color ?>;animation:confettiFall <?= $dur ?>s linear <?= $delay ?>s infinite;"></div>
  <?php endfor; ?>

  <!-- Hero content grid -->
  <div class="relative mx-auto grid max-w-7xl items-center gap-8 px-4 pb-14 pt-12 sm:gap-10 sm:px-5 sm:py-20 lg:grid-cols-[48%_52%] lg:gap-8 lg:px-8 lg:py-24 xl:py-28">

    <!-- LEFT: Event info -->
    <div class="min-w-0 text-center lg:text-left">

      <!-- Eyebrow -->
      <div class="animate-fade-up mb-4 inline-flex max-w-full items-center gap-2 rounded-full border border-orange-primary/40 bg-white/8 px-3 py-1.5 backdrop-blur-sm sm:mb-5 sm:px-4" style="border-color:rgba(249,115,22,.4);background:rgba(255,255,255,.08)">
        <span class="h-1.5 w-1.5 flex-shrink-0 rounded-full" style="background:#F97316"></span>
        <span class="min-w-0 text-[10px] font-bold uppercase tracking-[0.18em] text-white/90 sm:text-xs sm:tracking-[0.3em]">Festival of Unity &amp; Faith</span>
      </div>

      <!-- Main headline -->
      <h1 class="animate-fade-up font-display leading-[0.9] drop-shadow-2xl sm:leading-none" style="animation-delay:.1s">
        <span class="block text-[4rem] font-black sm:text-7xl lg:text-8xl xl:text-9xl" style="color:#F4C96B">COLOR</span>
        <span class="block text-[4rem] font-black sm:text-7xl lg:text-8xl xl:text-9xl" style="color:#F4C96B">OF</span>
        <span class="block text-[4rem] font-black sm:text-7xl lg:text-8xl xl:text-9xl" style="color:#ffffff">GRACE</span>
      </h1>

      <!-- OTW logo -->
      <div class="animate-fade-up mt-4 flex justify-center sm:mt-5 lg:justify-start" style="animation-delay:.2s">
        <img src="<?= e(BASE_URL) ?>/assets/img/logo-otw.png" alt="One Truth Way" class="h-auto w-40 object-contain sm:w-56 lg:w-64">
      </div>

      <!-- Event details -->
      <div class="animate-fade-up mx-auto mt-6 flex max-w-sm flex-col items-stretch gap-3 sm:mt-8 lg:mx-0 lg:items-start" style="animation-delay:.3s">
        <!-- Date -->
        <div class="flex min-w-0 items-center justify-center gap-3 lg:justify-start">
          <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg" style="background:rgba(249,115,22,.25)">
            <svg class="h-4 w-4" fill="none" stroke="#F97316" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          </div>
          <span class="min-w-0 text-sm font-bold text-white sm:text-base">22 AUGUST 2026</span>
        </div>
        <!-- Location -->
        <div class="flex min-w-0 items-center justify-center gap-3 lg:justify-start">
          <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg" style="background:rgba(249,115,22,.25)">
            <svg class="h-4 w-4" fill="none" stroke="#F97316" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          </div>
          <span class="min-w-0 max-w-[18rem] break-words text-center text-sm font-bold leading-snug text-white sm:max-w-none sm:text-base lg:text-left">ROYAL PHOENIX RESTAURANT · 2ND FLOOR</span>
        </div>
        <!-- Time -->
        <div class="flex min-w-0 items-center justify-center gap-3 lg:justify-start">
          <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg" style="background:rgba(249,115,22,.25)">
            <svg class="h-4 w-4" fill="none" stroke="#F97316" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <span class="min-w-0 text-sm font-bold text-white sm:text-base">OPEN GATE 14.00 WITA</span>
        </div>
      </div>

      <!-- CTA buttons -->
      <div class="animate-fade-up mx-auto mt-8 flex max-w-sm flex-col items-stretch gap-3 sm:mt-10 sm:max-w-none sm:flex-row sm:justify-center lg:mx-0 lg:justify-start" style="animation-delay:.4s">
        <a href="<?= e(BASE_URL) ?>/register.php"
           class="btn-cta group inline-flex w-full items-center justify-center gap-2 rounded-2xl px-6 py-3.5 text-sm font-extrabold text-white transition sm:w-auto sm:px-8 sm:py-4 sm:text-base">
          DAFTAR SEKARANG
          <svg class="h-5 w-5 transition group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </a>
        <a href="#tentang"
           class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border-2 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-white/10 sm:w-auto sm:px-8 sm:py-4 sm:text-base"
           style="border-color:rgba(217,164,65,.6);color:#F4C96B">
          LIHAT DETAIL ACARA
        </a>
      </div>

    </div>

    <!-- RIGHT: Artwork -->
    <div class="animate-fade-in relative mx-auto flex w-full max-w-[20rem] items-center justify-center sm:max-w-md lg:max-w-none" style="animation-delay:.25s">
      <!-- Glow ring -->
      <div class="absolute inset-0 rounded-3xl opacity-30 blur-3xl pointer-events-none sm:opacity-40" style="background:radial-gradient(ellipse at center,rgba(249,115,22,.5),rgba(201,31,37,.3),transparent 70%)"></div>

      <!-- Floating confetti around artwork -->
      <div class="absolute -top-4 -left-4 h-3 w-3 rounded-full animate-float" style="background:#F4C96B;animation-delay:.5s"></div>
      <div class="absolute -top-6 right-12 h-2 w-2 rounded-sm animate-float-slow" style="background:#F97316;animation-delay:1s"></div>
      <div class="absolute bottom-8 -left-6 h-2.5 w-2.5 rounded-full animate-float" style="background:#C91F25;animation-delay:1.5s"></div>
      <div class="absolute -bottom-4 right-4 h-3 w-3 rounded-sm animate-float-slow" style="background:#D9A441;animation-delay:.8s"></div>

      <!-- Artwork image -->
      <div class="artwork-glow relative w-full max-w-lg lg:max-w-none">
        <img src="<?= e(BASE_URL) ?>/assets/img/content.png"
             alt="Color of Grace — One Truth Way"
             class="mx-auto h-auto max-h-[42vh] w-full object-contain sm:max-h-[52vh] lg:max-h-[75vh]"
             style="">
      </div>
    </div>
  </div>

  <!-- Bottom wave -->
  <div class="absolute bottom-0 left-0 right-0 pointer-events-none">
    <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" class="w-full">
      <path d="M0 80L1440 80L1440 30C1200 70 960 10 720 40C480 70 240 10 0 30L0 80Z" fill="#FFF9ED"/>
    </svg>
  </div>
</section>

<!-- ============================================================ -->
<!-- VALUES STRIP                                                  -->
<!-- ============================================================ -->
<section id="tentang" class="relative -mt-1 pb-16 pt-4" style="background:#FFF9ED">
  <div class="mx-auto max-w-6xl px-5">
    <div class="reveal grid gap-5 sm:grid-cols-3">

      <?php
      $values = [
        [
          'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
          'label' => 'PERSATUAN',
          'desc'  => 'Bersatu dalam kasih sebagai satu tubuh Kristus',
          'color' => '#F97316',
          'bg'    => 'rgba(249,115,22,.12)',
        ],
        [
          'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>',
          'label' => 'IMAN',
          'desc'  => 'Berpegang teguh pada kebenaran yang mengubahkan',
          'color' => '#C91F25',
          'bg'    => 'rgba(201,31,37,.12)',
        ],
        [
          'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>',
          'label' => 'KEBERSAMAAN',
          'desc'  => 'Merayakan anugerah dalam sukacita dan kebersamaan',
          'color' => '#D9A441',
          'bg'    => 'rgba(217,164,65,.12)',
        ],
      ];
      foreach ($values as $v): ?>
      <div class="group rounded-3xl border bg-white p-8 text-center shadow-lg transition hover:-translate-y-1 hover:shadow-xl" style="border-color:rgba(217,164,65,.2)">
        <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl transition group-hover:scale-110" style="background:<?= $v['bg'] ?>">
          <svg class="h-8 w-8" fill="none" stroke="<?= $v['color'] ?>" stroke-width="1.8" viewBox="0 0 24 24">
            <?= $v['icon'] ?>
          </svg>
        </div>
        <h3 class="font-display text-lg font-black tracking-wide" style="color:<?= $v['color'] ?>"><?= $v['label'] ?></h3>
        <p class="mt-2 text-sm leading-relaxed text-slate-500"><?= $v['desc'] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============================================================ -->
<!-- REGISTRATION SECTION                                          -->
<!-- ============================================================ -->
<section id="informasi" class="py-14 sm:py-20" style="background:#FFF4D6">
  <div class="mx-auto max-w-7xl px-4 sm:px-5 lg:px-8">

    <!-- Section heading -->
    <div class="reveal mb-10 text-center sm:mb-14">
      <p class="mb-2 text-[10px] font-bold uppercase tracking-[0.24em] sm:text-xs sm:tracking-[0.35em]" style="color:#F97316">Bergabunglah Bersama Kami</p>
      <h2 class="font-display text-3xl font-black leading-tight text-slate-900 sm:text-5xl">
        Jadilah Bagian dari<br>
        <span style="color:#C91F25">COLOR OF GRACE</span><br>
        <span class="text-xl font-bold text-slate-500 sm:text-3xl">— ONE TRUTH WAY</span>
      </h2>
      <p class="mx-auto mt-5 max-w-2xl text-base leading-relaxed text-slate-600">
        Sebuah perayaan iman, persatuan, dan kebersamaan bagi generasi muda untuk memuliakan Tuhan dan menjadi terang di mana pun kita berada.
      </p>
    </div>

    <!-- Two-column layout -->
    <div class="reveal grid gap-10 lg:grid-cols-2 lg:gap-16">

      <!-- LEFT: Event summary -->
      <div class="flex flex-col gap-8">

        <!-- Decorative festival visual -->
        <div class="relative overflow-hidden rounded-3xl p-5 sm:p-8" style="background:linear-gradient(135deg,#07152D 0%,#1a0a0a 100%)">
          <!-- Glow -->
          <div class="absolute inset-0 pointer-events-none" style="background:radial-gradient(ellipse at 70% 50%,rgba(249,115,22,.25),transparent 65%)"></div>
          <!-- Confetti dots -->
          <div class="absolute top-4 right-8 h-2 w-2 rounded-full" style="background:#F4C96B;opacity:.7"></div>
          <div class="absolute top-12 right-20 h-1.5 w-1.5 rounded-full" style="background:#F97316;opacity:.6"></div>
          <div class="absolute bottom-6 left-10 h-2 w-2 rounded-sm" style="background:#C91F25;opacity:.6"></div>

          <div class="relative flex flex-col items-center gap-5 text-center sm:flex-row sm:text-left">
            <div class="flex-shrink-0">
              <div class="text-center">
                <div class="font-display text-5xl font-black leading-none sm:text-6xl" style="color:#F4C96B">22</div>
                <div class="mt-1 text-xs font-bold uppercase tracking-widest text-white/70 sm:text-sm">AUGUST</div>
                <div class="font-display text-xl font-black text-white sm:text-2xl">2026</div>
              </div>
            </div>
            <div class="hidden h-20 w-px sm:block" style="background:rgba(255,255,255,.15)"></div>
            <div class="flex min-w-0 flex-col gap-3">
              <div class="flex min-w-0 items-start justify-center gap-2 sm:justify-start">
                <svg class="mt-0.5 h-4 w-4 flex-shrink-0" fill="none" stroke="#F97316" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <div class="min-w-0">
                  <p class="break-words text-sm font-bold leading-snug text-white">ROYAL PHOENIX RESTAURANT</p>
                  <p class="text-xs text-white/60">2ND FLOOR</p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="#F97316" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                  <p class="text-xs text-white/60">OPEN GATE</p>
                  <p class="text-sm font-bold text-white">14.00 WITA</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Info notes -->
        <div class="rounded-3xl border bg-white p-7 shadow-sm" style="border-color:rgba(217,164,65,.25)">
          <h3 class="mb-5 font-display text-lg font-black text-slate-900">Perlu Diperhatikan</h3>
          <ul class="space-y-4">
            <?php
            $catatan = [
              ['Setiap pendaftaran ditinjau panitia terlebih dahulu.', '#F97316'],
              ['Barcode dikirim melalui email setelah pendaftaranmu disetujui.', '#C91F25'],
              ['Simpan barcode di ponsel — tunjukkan saat datang, 22 Agustus 2026.', '#D9A441'],
              ['Cukup satu kali pindai barcode di pintu masuk.', '#F97316'],
            ];
            foreach ($catatan as [$c, $col]): ?>
            <li class="flex gap-3">
              <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" stroke="<?= $col ?>" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span class="text-sm leading-relaxed text-slate-600"><?= e($c) ?></span>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>

        <!-- Stats -->
        <div class="mx-auto w-full max-w-xs sm:mx-0">
          <div class="rounded-2xl p-5 text-center" style="background:linear-gradient(135deg,rgba(249,115,22,.12),rgba(201,31,37,.08))">
            <p class="font-display text-3xl font-black" style="color:#F97316"><?= max(0, $hari_lagi) ?></p>
            <p class="mt-1 text-xs font-semibold text-slate-500">Hari Lagi</p>
          </div>
        </div>
      </div>

      <!-- RIGHT: Registration form -->
      <div id="daftar" class="min-w-0">
        <div class="rounded-3xl bg-white p-5 shadow-2xl sm:p-10" style="box-shadow:0 20px 60px rgba(0,0,0,.12)">
          <h3 class="font-display text-2xl font-black text-slate-900">Daftar Sekarang</h3>
          <p class="mt-2 text-sm leading-relaxed text-slate-500">
            Isi data diri kamu di bawah ini untuk melakukan registrasi.<br>
            Semua data akan digunakan hanya untuk keperluan acara ini.
          </p>

          <!-- Inline registration form -->
          <form id="reg-form" method="POST" action="<?= e(BASE_URL) ?>/register.php" class="mt-7 space-y-5" novalidate>
            <?= csrf_field() ?>

            <!-- Nama -->
            <div>
              <label for="f-nama" class="mb-1.5 block text-sm font-semibold text-slate-700">
                Nama Lengkap <span style="color:#C91F25">*</span>
              </label>
              <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                  <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </span>
                <input id="f-nama" name="nama" type="text" autocomplete="name"
                       placeholder="Masukkan nama lengkap"
                       class="field-input w-full rounded-xl border-2 border-slate-200 bg-slate-50 py-3.5 pl-10 pr-4 text-sm text-slate-800 placeholder-slate-400 transition"
                       data-required data-label="Nama">
              </div>
              <p class="field-error mt-1.5 hidden text-xs font-medium text-red-500" data-for="f-nama"></p>
            </div>

            <!-- Email -->
            <div>
              <label for="f-email" class="mb-1.5 block text-sm font-semibold text-slate-700">
                Email <span style="color:#C91F25">*</span>
              </label>
              <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                  <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
                <input id="f-email" name="email" type="email" autocomplete="email"
                       placeholder="Masukkan alamat email"
                       class="field-input w-full rounded-xl border-2 border-slate-200 bg-slate-50 py-3.5 pl-10 pr-4 text-sm text-slate-800 placeholder-slate-400 transition"
                       data-required data-label="Email" data-type="email">
              </div>
              <p class="field-error mt-1.5 hidden text-xs font-medium text-red-500" data-for="f-email"></p>
            </div>

            <!-- WhatsApp -->
            <div>
              <label for="f-wa" class="mb-1.5 block text-sm font-semibold text-slate-700">
                No. WhatsApp <span style="color:#C91F25">*</span>
              </label>
              <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                  <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </span>
                <input id="f-wa" name="whatsapp" type="tel" autocomplete="tel"
                       placeholder="08xxxxxxxxxx"
                       class="field-input w-full rounded-xl border-2 border-slate-200 bg-slate-50 py-3.5 pl-10 pr-4 text-sm text-slate-800 placeholder-slate-400 transition"
                       data-required data-label="No. WhatsApp">
              </div>
              <p class="field-error mt-1.5 hidden text-xs font-medium text-red-500" data-for="f-wa"></p>
            </div>

            <!-- Asal Jemaat -->
            <div>
              <label for="f-gereja" class="mb-1.5 block text-sm font-semibold text-slate-700">
                Asal Jemaat <span style="color:#C91F25">*</span>
              </label>
              <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                  <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </span>
                <input id="f-gereja" name="gereja" type="text"
                       placeholder="Masukkan asal jemaat"
                       class="field-input w-full rounded-xl border-2 border-slate-200 bg-slate-50 py-3.5 pl-10 pr-4 text-sm text-slate-800 placeholder-slate-400 transition"
                       data-required data-label="Asal Jemaat">
              </div>
              <p class="field-error mt-1.5 hidden text-xs font-medium text-red-500" data-for="f-gereja"></p>
            </div>

            <!-- Info dari (optional) -->
            <div>
              <label for="f-info" class="mb-1.5 block text-sm font-semibold text-slate-700">
                Mengetahui OTW dari mana
                <span class="ml-1 rounded-full px-2 py-0.5 text-xs font-medium" style="background:rgba(217,164,65,.15);color:#D9A441">Opsional</span>
              </label>
              <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                  <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <select id="f-info" name="info_dari"
                        class="field-input w-full appearance-none rounded-xl border-2 border-slate-200 bg-slate-50 py-3.5 pl-10 pr-10 text-sm text-slate-800 transition">
                  <option value="">— Pilih sumber informasi —</option>
                  <option value="Instagram">Instagram</option>
                  <option value="Media Sosial">TikTok</option>
                  <option value="Teman">Teman</option>
                  <option value="Gereja">Jemaat</option>
                  <option value="Poster/Flyer">Poster / Flyer</option>
                  <option value="Lainnya">Lainnya</option>
                </select>
                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                  <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </span>
              </div>
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="btn-cta group mt-2 flex w-full items-center justify-center gap-2 rounded-2xl py-4 text-base font-extrabold text-white transition">
              <span>DAFTAR SEKARANG</span>
              <svg class="h-5 w-5 transition group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </button>

            <p class="mt-3 text-center text-xs text-slate-400">
              Pendaftaran akan ditinjau panitia sebelum barcode dikirim ke email kamu.
            </p>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================ -->
<!-- FOOTER                                                        -->
<!-- ============================================================ -->
<footer id="kontak" style="background:#07152D">
  <div class="mx-auto max-w-7xl px-5 py-16 lg:px-8">
    <div class="grid gap-12 lg:grid-cols-3 lg:gap-8">

      <!-- LEFT: OTW -->
      <div>
        <img src="<?= e(BASE_URL) ?>/assets/img/logo-otw.png" alt="One Truth Way" class="mb-5 h-12 w-auto object-contain">
        <p class="text-sm leading-relaxed" style="color:rgba(255,255,255,.55)">
          ONE TRUTH WAY adalah gerakan anak muda untuk membawa satu kebenaran, satu iman, dan satu tujuan bagi kemuliaan Tuhan.
        </p>
        <!-- Social icons -->
        <div class="mt-6 flex gap-3">
          <?php
          $socials = [
            ['Instagram', '<path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>'],
            ['TikTok',    '<path d="M9 12a4 4 0 104 4V4a5 5 0 005 5"/>'],
            ['YouTube',   '<path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58A2.78 2.78 0 003.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.95A29 29 0 0023 12a29 29 0 00-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/>'],
            ['WhatsApp',  '<path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/>'],
          ];
          foreach ($socials as [$name, $icon]): ?>
          <a href="#" aria-label="<?= $name ?>"
             class="flex h-9 w-9 items-center justify-center rounded-xl transition hover:-translate-y-0.5"
             style="background:rgba(255,255,255,.08)">
            <svg class="h-4 w-4" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="1.8" viewBox="0 0 24 24">
              <?= $icon ?>
            </svg>
          </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- CENTER: Event branding -->
      <div class="text-center">
        <p class="mb-1 text-xs font-bold uppercase tracking-[0.3em]" style="color:#F97316">Festival of Unity &amp; Faith</p>
        <h3 class="font-display text-3xl font-black" style="color:#F4C96B">COLOR OF GRACE</h3>
        <p class="mt-1 font-display text-lg font-bold text-white/70">ONE TRUTH WAY</p>
        <div class="mx-auto mt-5 h-px w-24" style="background:linear-gradient(90deg,transparent,rgba(217,164,65,.5),transparent)"></div>
        <p class="mt-5 text-sm" style="color:rgba(255,255,255,.5)">22 Agustus 2026</p>
        <p class="text-sm" style="color:rgba(255,255,255,.5)">Royal Phoenix Restaurant · Lantai 2</p>
        <a href="<?= e(BASE_URL) ?>/register.php"
           class="btn-cta mt-6 inline-flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold text-white transition">
          Daftar Sekarang →
        </a>
      </div>

      <!-- RIGHT: COI logo -->
      <div class="flex flex-col items-center lg:items-end">
        <p class="mb-4 text-xs font-bold uppercase tracking-[0.25em]" style="color:rgba(255,255,255,.4)">Presented by</p>
        <img src="<?= e(BASE_URL) ?>/assets/img/logo-coi.png" alt="COI Ministry" class="h-20 w-auto object-contain">
        <p class="mt-3 text-sm font-semibold text-white/60">COI Ministry</p>
      </div>
    </div>

    <!-- Divider -->
    <div class="mt-12 border-t pt-8" style="border-color:rgba(255,255,255,.08)">
      <div class="flex flex-col items-center justify-between gap-4 text-center sm:flex-row">
        <p class="text-xs" style="color:rgba(255,255,255,.35)">
          &copy; <?= date('Y') ?> ONE TRUTH WAY. All rights reserved.
        </p>
        <a href="<?= e(BASE_URL) ?>/admin/login.php"
           class="text-xs transition hover:text-white/60" style="color:rgba(255,255,255,.25)">Admin</a>
      </div>
    </div>
  </div>
</footer>

<!-- ============================================================ -->
<!-- SCRIPTS                                                       -->
<!-- ============================================================ -->
<script>
// ── Sticky header ──────────────────────────────────────────────
const header = document.getElementById('site-header');
function updateHeader() {
  if (window.scrollY > 60) {
    header.style.background = 'rgba(7,21,45,.95)';
    header.style.backdropFilter = 'blur(12px)';
    header.style.boxShadow = '0 2px 20px rgba(0,0,0,.3)';
  } else {
    header.style.background = 'transparent';
    header.style.backdropFilter = 'none';
    header.style.boxShadow = 'none';
  }
}
window.addEventListener('scroll', updateHeader, { passive: true });
updateHeader();

// ── Mobile menu ────────────────────────────────────────────────
const toggle   = document.getElementById('menu-toggle');
const menu     = document.getElementById('mobile-menu');
const iconOpen  = document.getElementById('icon-open');
const iconClose = document.getElementById('icon-close');
let menuOpen = false;

toggle.addEventListener('click', () => {
  menuOpen = !menuOpen;
  if (menuOpen) {
    menu.classList.remove('hidden');
    iconOpen.classList.add('hidden');
    iconClose.classList.remove('hidden');
  } else {
    menu.classList.add('hidden');
    iconOpen.classList.remove('hidden');
    iconClose.classList.add('hidden');
  }
});

// Close menu on link click
menu.querySelectorAll('a').forEach(a => {
  a.addEventListener('click', () => {
    menu.classList.add('hidden');
    iconOpen.classList.remove('hidden');
    iconClose.classList.add('hidden');
    menuOpen = false;
  });
});

// ── Scroll reveal ──────────────────────────────────────────────
const reveals = document.querySelectorAll('.reveal');
const observer = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.classList.add('visible');
      observer.unobserve(e.target);
    }
  });
}, { threshold: 0.12 });
reveals.forEach(el => observer.observe(el));

// ── Client-side form validation ────────────────────────────────
const form = document.getElementById('reg-form');
if (form) {
  const showError = (id, msg) => {
    const input = document.getElementById(id);
    const errEl = form.querySelector(`[data-for="${id}"]`);
    if (input)  input.classList.add('error');
    if (errEl) { errEl.textContent = msg; errEl.classList.remove('hidden'); }
  };
  const clearError = (id) => {
    const input = document.getElementById(id);
    const errEl = form.querySelector(`[data-for="${id}"]`);
    if (input)  input.classList.remove('error');
    if (errEl) { errEl.textContent = ''; errEl.classList.add('hidden'); }
  };

  // Live clear on input
  form.querySelectorAll('.field-input').forEach(inp => {
    inp.addEventListener('input', () => clearError(inp.id));
  });

  form.addEventListener('submit', (e) => {
    let valid = true;

    // Nama
    const nama = document.getElementById('f-nama').value.trim();
    clearError('f-nama');
    if (!nama) { showError('f-nama', 'Nama wajib diisi'); valid = false; }
    else if (nama.length < 3) { showError('f-nama', 'Nama minimal 3 karakter'); valid = false; }

    // Email
    const email = document.getElementById('f-email').value.trim();
    clearError('f-email');
    if (!email) { showError('f-email', 'Email wajib diisi'); valid = false; }
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showError('f-email', 'Format email tidak valid'); valid = false; }

    // WhatsApp
    const wa = document.getElementById('f-wa').value.trim();
    clearError('f-wa');
    if (!wa) { showError('f-wa', 'No. WhatsApp wajib diisi'); valid = false; }
    else if (wa.replace(/\D/g,'').length < 10) { showError('f-wa', 'Nomor WhatsApp tidak valid'); valid = false; }

    // Gereja
    const gereja = document.getElementById('f-gereja').value.trim();
    clearError('f-gereja');
    if (!gereja) { showError('f-gereja', 'Asal jemaat wajib diisi'); valid = false; }

    if (!valid) {
      e.preventDefault();
      // Scroll to first error
      const firstErr = form.querySelector('.field-input.error');
      if (firstErr) firstErr.scrollIntoView({ behavior:'smooth', block:'center' });
    }
  });
}
</script>

</body>
</html>
