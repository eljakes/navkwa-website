<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Navkwa Group Ltd. - Building Intelligent Software for Africa's Future</title>
  <meta name="description" content="Navkwa Group Ltd. designs and builds enterprise-grade software, custom platforms, and long-term technology partnerships for businesses across Africa.">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">

  <script defer src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <script defer src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
  <script>
    window.Navkwa = {
      routes: {
        contact: "{{ route('contact.store') }}",
        chatMessages: "{{ route('chat.messages.store') }}",
        chatHistory: "{{ route('chat.messages.index') }}"
      }
    };
  </script>
  <script defer src="{{ asset('assets/js/main.js') }}"></script>
</head>
<body>
  <div id="loader">
    <div class="mark font-display">NAVKWA</div>
    <div class="loader-bar"><span id="loaderFill"></span></div>
    <div class="loader-pct" id="loaderPct">00%</div>
  </div>

  <header id="siteHeader">
    <div class="wrap">
      <nav>
        <a href="#hero" class="logo"><span class="dot"></span>Navkwa</a>
        <ul class="nav-links" id="navLinks">
          <li><a href="{{ route('about.index') }}">About</a></li>
          <li><a href="{{ route('services.index') }}">Services</a></li>
          <li><a href="{{ route('products.index') }}">Products</a></li>
          <li><a href="{{ route('industries.index') }}">Industries</a></li>
          <li><a href="{{ route('work.index') }}">Work</a></li>
          <li><a href="{{ route('contact.index') }}">Contact</a></li>
          <li><a href="{{ route('payments.create') }}">Payments</a></li>
        </ul>
        <div class="nav-cta-group">
          <button class="menu-toggle" id="menuToggle" aria-label="Menu" aria-controls="navLinks" aria-expanded="false">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
          </button>
        </div>
      </nav>
    </div>
  </header>

  <main>
    <section id="hero" class="homepage-hero">
      <figure class="hero-visual hero-video-visual" aria-label="Navkwa homepage technology video">
        <video class="hero-video" autoplay muted loop playsinline preload="metadata">
          <source src="{{ asset('assets/videos/homepage-hero.mp4') }}" type="video/mp4">
          <source src="{{ asset('assets/videos/homepage-hero.mov') }}" type="video/quicktime">
        </video>
      </figure>
      <div class="wrap hero-foreground">
        <div class="hero-layout">
          <div class="hero-content">
            <h1>Building intelligent software for <span class="grad-text">Africa&rsquo;s future.</span></h1>
            <div class="hero-actions">
              <a href="{{ route('contact.index') }}" class="btn btn-primary btn-lg">Book a Discovery Call &rarr;</a>
              <a href="{{ route('work.index') }}" class="btn btn-ghost btn-lg">View Work</a>
            </div>
          </div>
        </div>
      </div>
      <div class="scroll-cue"><span>Scroll</span><span class="line"></span></div>
    </section>

    <section class="home-snapshot theme-white">
      <div class="wrap">
        <div class="home-snapshot-layout">
          <figure class="home-robot-media" data-reveal>
            <img src="{{ asset('assets/images/home/robot-technology-system.png') }}" alt="A professional AI robot interacting with digital system intelligence" loading="lazy" decoding="async">
            <span class="robot-orbit orbit-one" aria-hidden="true"></span>
            <span class="robot-orbit orbit-two" aria-hidden="true"></span>
            <span class="robot-scan-line" aria-hidden="true"></span>
          </figure>
          <div class="home-snapshot-content">
            <div class="section-head" data-reveal>
              <div class="eyebrow">// What Navkwa Does</div>
              <h2>Practical software systems for companies that need control, clarity, and scale.</h2>
              <p>Navkwa designs custom platforms, SaaS products, and business operations systems for African organisations moving beyond scattered tools and manual workflows.</p>
            </div>
            <div class="home-snapshot-grid" data-reveal>
              <a class="home-snapshot-card" href="{{ route('services.index') }}">
                <h3>Software Services</h3>
                <p>Custom software, mobile apps, SaaS platforms, AI automation, and cloud integrations.</p>
              </a>
              <a class="home-snapshot-card" href="{{ route('products.index') }}">
                <h3>Focused Products</h3>
                <p>Industry platforms starting with construction, field services, and operations portals.</p>
              </a>
              <a class="home-snapshot-card" href="{{ route('about.index') }}">
                <h3>Long-Term Delivery</h3>
                <p>Secure architecture, documentation, ownership clarity, support, and post-launch improvement.</p>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="home-product-spotlight theme-soft">
      <div class="wrap">
        <div class="home-product-panel" data-reveal>
          <article>
            <span class="badge-product">Active Product</span>
            <h2 class="font-display">Navkwa Build</h2>
            <p>The Construction Operating System for modern contractors: projects, procurement, budgets, approvals, site progress, and executive visibility in one connected platform.</p>
            <div class="home-panel-actions">
              <a href="{{ route('products.navkwa-build') }}" class="btn btn-primary btn-lg">Explore Navkwa Build</a>
              <a href="{{ route('products.index') }}" class="btn btn-ghost btn-lg">View All Products</a>
            </div>
          </article>
          <figure>
            <img src="{{ asset('assets/images/products/navkwa-build-erp-system.png') }}" alt="Navkwa Build ERP dashboard preview" loading="lazy" decoding="async">
          </figure>
        </div>
      </div>
    </section>

    <section class="home-pathways theme-white">
      <div class="wrap">
        <div class="section-head" data-reveal>
          <div class="eyebrow">// Explore Navkwa</div>
          <h2>Choose where you want to go next.</h2>
        </div>
        <div class="home-pathway-grid" data-reveal>
          <a href="{{ route('services.index') }}"><span>Services</span><strong>What we build</strong></a>
          <a href="{{ route('industries.index') }}"><span>Industries</span><strong>Where we focus</strong></a>
          <a href="{{ route('work.index') }}"><span>Work</span><strong>Products and systems</strong></a>
          <a href="{{ route('contact.index') }}"><span>Contact</span><strong>Start a conversation</strong></a>
        </div>
      </div>
    </section>

    <section class="home-final-cta">
      <div class="wrap">
        <div class="cta-panel" data-reveal>
          <h2>Build software around how your business actually works.</h2>
          <p>Talk to Navkwa about the system you want to build, improve, automate, or replace.</p>
          <div class="hero-actions">
            <a href="{{ route('contact.index') }}" class="btn btn-primary btn-lg">Book a Discovery Call &rarr;</a>
            <a href="{{ route('about.index') }}" class="btn btn-ghost btn-lg">Learn About Us</a>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer>
    <div class="wrap">
      <div class="footer-grid">
        <div>
          <a href="#hero" class="logo footer-brand"><span class="dot"></span>Navkwa</a>
          <p class="footer-copy">Building intelligent software that powers African businesses &mdash; from first prototype to enterprise scale.</p>
        </div>
        <div>
          <h6>Company</h6>
          <ul><li><a href="{{ route('about.index') }}">About</a></li><li><a href="{{ route('work.index') }}">Work</a></li><li><a href="{{ route('contact.index') }}">Contact</a></li><li><a href="{{ route('payments.create') }}">Payments</a></li></ul>
        </div>
        <div>
          <h6>Services</h6>
          <ul><li><a href="{{ route('services.index') }}">Custom Software</a></li><li><a href="{{ route('services.index') }}">Mobile Applications</a></li><li><a href="{{ route('services.index') }}">SaaS Development</a></li><li><a href="{{ route('services.index') }}">Cloud and Integrations</a></li><li><a href="{{ route('services.index') }}">AI and Automation</a></li></ul>
        </div>
        <div>
          <h6>Products</h6>
          <ul><li><a href="{{ route('products.navkwa-build') }}">Navkwa Build</a></li><li><a href="{{ route('products.index') }}">FixAm</a></li><li><a href="{{ route('products.index') }}">Product Roadmap</a></li><li><a href="{{ route('products.index') }}">FAQ</a></li></ul>
        </div>
        <div>
          <h6>Legal</h6>
          <ul><li><a href="{{ route('legal.privacy') }}">Privacy Policy</a></li><li><a href="{{ route('legal.terms') }}">Terms of Use</a></li><li><a href="{{ route('legal.cookies') }}">Cookie Policy</a></li></ul>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2026 Navkwa Group Ltd. All rights reserved.</p>
        <p class="footer-links-inline"><a href="{{ route('payments.create') }}">Payments</a><a href="{{ route('legal.privacy') }}">Privacy Policy</a><a href="{{ route('legal.terms') }}">Terms of Use</a><a href="{{ route('legal.cookies') }}">Cookie Policy</a></p>
      </div>
    </div>
  </footer>

  <button id="chat-launcher" aria-label="Open chat">
    <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
  </button>
  <div id="chat-panel">
    <div class="chat-head"><span class="chat-status-dot"></span><div><strong>Navkwa Support</strong><span>Replies during business hours</span></div></div>
    <div class="chat-body" id="chatBody">
      <div class="chat-bubble">Hi &#128075; - tell us a bit about your project and we&rsquo;ll point you to the right person.</div>
    </div>
    <p class="chat-status-line" id="chatStatus" role="status"></p>
    <form class="chat-foot" id="chatForm">
      <input id="chatInput" type="text" placeholder="Type a message..." autocomplete="off" maxlength="2000" required>
      <button aria-label="Send" type="submit">&rarr;</button>
    </form>
  </div>
</body>
</html>
