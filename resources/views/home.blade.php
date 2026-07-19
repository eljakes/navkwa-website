<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Navkwa Group Ltd. - Building Intelligent Software for Africa's Future</title>
  <meta name="description" content="Navkwa Group Ltd. designs and builds enterprise-grade software, custom platforms, and long-term technology partnerships for businesses across Africa.">
  <meta name="csrf-token" content="{{ csrf_token() }}">

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
          <li><a href="#services">Services</a></li>
          <li><a href="#industries">Industries</a></li>
          <li><a href="#roadmap">Products</a></li>
          <li><a href="#work">Work</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
        <div class="nav-cta-group">
          <a href="#contact" class="btn btn-primary btn-sm">Book Consultation</a>
          <button class="menu-toggle" id="menuToggle" aria-label="Menu" aria-controls="navLinks" aria-expanded="false">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
          </button>
        </div>
      </nav>
    </div>
  </header>

  <main>
    <section id="hero">
      <div class="hero-bg-drift" id="heroBgDrift" aria-hidden="true"></div>
      <div class="hero-shade"></div>
      <canvas id="hero-canvas"></canvas>
      <div class="hero-glow g1"></div>
      <div class="hero-glow g2"></div>
      <div class="wrap">
        <div class="hero-layout">
          <div class="hero-content">
            <div class="eyebrow font-mono">Navkwa Group Ltd. &mdash; Systems Engineering</div>
            <h1>Building intelligent software for <span class="grad-text">Africa&rsquo;s future.</span></h1>
            <p class="lead">We help ambitious businesses automate operations, cut costs, and scale through enterprise-grade software built to run for a decade, not a demo.</p>
            <div class="hero-actions">
              <a href="#contact" class="btn btn-primary btn-lg">Book Consultation</a>
            </div>
            <div class="hero-strip">
              <div><span class="num font-display">10+</span><span class="lbl">SOLUTIONS SHIPPED</span></div>
              <div><span class="num font-display">100%</span><span class="lbl">CLIENT COMMITMENT</span></div>
              <div><span class="num font-display">24/7</span><span class="lbl">ENGINEERING SUPPORT</span></div>
            </div>
          </div>

          <div class="hero-visual" aria-label="Navkwa office and technology carousel">
            <div class="hero-carousel" id="heroCarousel">
              @forelse($carouselSlides as $slide)
                <div class="hero-slide {{ $loop->first ? 'active' : '' }}" data-slide>
                  <img src="{{ $slide['url'] }}" alt="{{ $slide['alt'] }}">
                </div>
              @empty
                <div class="hero-slide hero-slide-fallback fallback-one active" data-slide></div>
                <div class="hero-slide hero-slide-fallback fallback-two" data-slide></div>
                <div class="hero-slide hero-slide-fallback fallback-three" data-slide></div>
              @endforelse
            </div>
            <div class="tech-hud" aria-hidden="true">
              <span class="hud-line l1"></span>
              <span class="hud-line l2"></span>
              <span class="hud-line l3"></span>
              <span class="hud-node n1"></span>
              <span class="hud-node n2"></span>
              <span class="hud-node n3"></span>
              <span class="hud-ring"></span>
            </div>
            <div class="hero-visual-meta" aria-hidden="true">
              <span class="font-mono">LIVE SYSTEMS</span>
              <span class="font-mono" id="heroSlideCounter">01 / {{ max($carouselSlides->count(), 3) }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="scroll-cue"><span>Scroll</span><span class="line"></span></div>
    </section>

    <section id="services">
      <div class="wrap">
        <div class="section-head" data-reveal>
          <div class="eyebrow">// 01 &mdash; Services</div>
          <h2>One partner, the full stack of your operation.</h2>
          <p>From first prototype to enterprise rollout, we design, build, and maintain the software your business runs on.</p>
        </div>
        <div class="services-grid" data-reveal>
          <div class="service-card">
            <span class="idx">01</span>
            <div><div class="si"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M4 4h16v16H4z"/><path d="M4 9h16"/></svg></div>
            <h3>Enterprise Software</h3></div>
            <p>ERP, CRM, and operations platforms built around how your business actually works, not a generic template.</p>
          </div>
          <div class="service-card">
            <span class="idx">02</span>
            <div><div class="si"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M3 4h18v12H3z"/><path d="M8 20h8M12 16v4"/></svg></div>
            <h3>Web Applications</h3></div>
            <p>Fast, secure, custom web platforms &mdash; from client portals to internal tools your team relies on daily.</p>
          </div>
          <div class="service-card">
            <span class="idx">03</span>
            <div><div class="si"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><rect x="6" y="2" width="12" height="20" rx="2"/><path d="M11 18h2"/></svg></div>
            <h3>Mobile Apps</h3></div>
            <p>Native-feel iOS and Android apps that extend your services into your customers&rsquo; pockets.</p>
          </div>
          <div class="service-card">
            <span class="idx">04</span>
            <div><div class="si"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"/></svg></div>
            <h3>Cloud Solutions</h3></div>
            <p>Infrastructure, migration, and scaling strategy so your systems stay fast under real-world load.</p>
          </div>
          <div class="service-card">
            <span class="idx">05</span>
            <div><div class="si"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg></div>
            <h3>AI &amp; Automation</h3></div>
            <p>Applied AI features and workflow automation that remove manual work from your operation.</p>
          </div>
          <div class="service-card">
            <span class="idx">06</span>
            <div><div class="si"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M4 17V7l8-4 8 4v10l-8 4-8-4z"/><path d="M4 7l8 4 8-4M12 11v10"/></svg></div>
            <h3>Digital Transformation</h3></div>
            <p>We map your paper and spreadsheet processes into connected, auditable digital systems.</p>
          </div>
          <div class="service-card">
            <span class="idx">07</span>
            <div><div class="si"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M9 3v18M15 3v18M3 9h18M3 15h18"/></svg></div>
            <h3>API Integration</h3></div>
            <p>Connect payments, logistics, banking, and government systems into one working pipeline.</p>
          </div>
          <div class="service-card">
            <span class="idx">08</span>
            <div><div class="si"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M12 20l-8-4V8l8-4 8 4v8z"/><path d="M12 12l8-4M12 12v8M12 12L4 8"/></svg></div>
            <h3>Product &amp; UI/UX Design</h3></div>
            <p>Interfaces designed for adoption &mdash; clear, fast, and built around how your users actually think.</p>
          </div>
          <div class="service-card">
            <span class="idx">09</span>
            <div><div class="si"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M12 2a10 10 0 100 20 10 10 0 000-20z"/><path d="M12 6v6l4 2"/></svg></div>
            <h3>Long-Term Support</h3></div>
            <p>Maintenance, monitoring, and continuous improvement. We stay after launch, not just for it.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="industries" class="tight">
      <div class="wrap">
        <div class="section-head" data-reveal>
          <div class="eyebrow">// 02 &mdash; Industries</div>
          <h2>Software built around how your sector actually operates.</h2>
        </div>
        <div class="industries-rail" data-reveal>
          <div class="industry-tile"><span class="ii">&#127959;&#65039;</span><h4>Construction</h4><span class="tag">Project &amp; site management</span></div>
          <div class="industry-tile"><span class="ii">&#127973;</span><h4>Healthcare</h4><span class="tag">Patient &amp; records systems</span></div>
          <div class="industry-tile"><span class="ii">&#127891;</span><h4>Education</h4><span class="tag">Campus &amp; learning platforms</span></div>
          <div class="industry-tile"><span class="ii">&#128666;</span><h4>Logistics</h4><span class="tag">Fleet &amp; supply chain</span></div>
          <div class="industry-tile"><span class="ii">&#128717;&#65039;</span><h4>Retail</h4><span class="tag">Commerce &amp; inventory</span></div>
          <div class="industry-tile"><span class="ii">&#127974;</span><h4>Finance</h4><span class="tag">Ledgers &amp; compliance</span></div>
          <div class="industry-tile"><span class="ii">&#127963;&#65039;</span><h4>Government</h4><span class="tag">Citizen platforms</span></div>
          <div class="industry-tile"><span class="ii">&#127981;</span><h4>Manufacturing</h4><span class="tag">Production &amp; inventory</span></div>
        </div>
      </div>
    </section>

    <section id="roadmap">
      <div class="wrap">
        <div class="section-head" data-reveal>
          <div class="eyebrow">// 03 &mdash; Innovation Roadmap</div>
          <h2>The products we&rsquo;re building next.</h2>
          <p>Alongside client projects, we&rsquo;re developing focused SaaS platforms for the industries we know best. Here&rsquo;s what&rsquo;s in progress.</p>
        </div>
        <div class="roadmap-grid" data-reveal>
          <div class="roadmap-card">
            <span class="badge-soon">Coming Soon</span>
            <h3>FixAm</h3>
            <p>This platform serves as a link between artisans and clients where artisans are booked directly based on their location and availability.</p>
          </div>
          <div class="roadmap-card">
            <span class="badge-soon">Coming Soon</span>
            <h3>Construction ERP</h3>
            <p>End-to-end site, budget, and materials management built for African contractors and developers.</p>
          </div>
          <div class="roadmap-card">
            <span class="badge-soon">Coming Soon</span>
            <h3>Hospital Management</h3>
            <p>Patient records, scheduling, and billing in one system designed for clinics and hospital groups.</p>
          </div>
          <div class="roadmap-card">
            <span class="badge-soon">Coming Soon</span>
            <h3>HR Platform</h3>
            <p>Payroll, attendance, and performance tools built for how African teams actually work.</p>
          </div>
          <div class="roadmap-card">
            <span class="badge-soon">Coming Soon</span>
            <h3>Inventory &amp; POS</h3>
            <p>Stock, sales, and multi-branch reporting for retailers who&rsquo;ve outgrown spreadsheets.</p>
          </div>
          <div class="roadmap-card idea-card">
            <h3>Have a product idea?</h3>
            <p class="idea-card-copy">We partner with businesses to co-build the next platform in this lineup.</p>
            <a href="#contact" class="btn btn-ghost btn-sm">Pitch us an idea &rarr;</a>
          </div>
        </div>
      </div>
    </section>

    <section id="why" class="tight">
      <div class="wrap">
        <div class="why-layout">
          <div data-reveal>
            <div class="eyebrow">// 04 &mdash; Why Navkwa</div>
            <h2 class="font-display why-title">Engineering discipline, applied to your business.</h2>
            <p class="why-copy">We&rsquo;re not a freelancer collective or a template shop. Every engagement follows the same production standard we&rsquo;d use for our own products.</p>
            <div class="stat-grid">
              <div class="stat-box"><div class="n font-display counter" data-target="10">0</div><div class="l">SOLUTIONS DELIVERED</div></div>
              <div class="stat-box"><div class="n font-display">100%</div><div class="l">CLIENT COMMITMENT</div></div>
              <div class="stat-box"><div class="n font-display">24/7</div><div class="l">SYSTEM SUPPORT</div></div>
            </div>
          </div>
          <div class="why-list" data-reveal>
            <div class="why-item"><span class="wn font-mono">01</span><div><h4>Enterprise-grade security</h4><p>Every build follows secure-by-default practices: encrypted data, hardened auth, and audited access.</p></div></div>
            <div class="why-item"><span class="wn font-mono">02</span><div><h4>Scalable architecture</h4><p>We design for the size you&rsquo;ll be in three years, not just the launch you need this quarter.</p></div></div>
            <div class="why-item"><span class="wn font-mono">03</span><div><h4>Agile delivery</h4><p>Short cycles, visible progress, and working software in your hands from week one.</p></div></div>
            <div class="why-item"><span class="wn font-mono">04</span><div><h4>Transparent communication</h4><p>You always know what&rsquo;s shipped, what&rsquo;s next, and what it costs &mdash; no surprises at invoice time.</p></div></div>
          </div>
        </div>
      </div>
    </section>

    <section id="work">
      <div class="wrap">
        <div class="section-head" data-reveal>
          <div class="eyebrow">// 05 &mdash; Selected Work</div>
          <h2>Systems we&rsquo;ve engineered.</h2>
          <p>A sample of the platforms and products we&rsquo;ve delivered &mdash; case studies with full detail available on request.</p>
        </div>
        <div class="portfolio-scroll" data-reveal>
          <div class="work-card">
            <div class="work-thumb"><div class="grid-lines"></div><svg viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.4"><rect x="3" y="4" width="18" height="14" rx="2"/><path d="M3 9h18"/></svg></div>
            <div class="work-body">
              <span class="wc-tag">Fintech</span>
              <h4>Merchant Payments Dashboard</h4>
              <p>A reconciliation and payout system processing transactions across multiple banking rails.</p>
              <div class="stack"><span>Next.js</span><span>Laravel</span><span>PostgreSQL</span></div>
            </div>
          </div>
          <div class="work-card">
            <div class="work-thumb"><div class="grid-lines"></div><svg viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.4"><path d="M3 21l6-14 4 8 3-6 5 12z"/></svg></div>
            <div class="work-body">
              <span class="wc-tag">Logistics</span>
              <h4>Fleet Tracking Platform</h4>
              <p>Real-time vehicle tracking and delivery routing built for a multi-branch distribution company.</p>
              <div class="stack"><span>React</span><span>Node</span><span>Redis</span></div>
            </div>
          </div>
          <div class="work-card">
            <div class="work-thumb"><div class="grid-lines"></div><svg viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.4"><circle cx="12" cy="12" r="8"/><path d="M12 8v4l3 2"/></svg></div>
            <div class="work-body">
              <span class="wc-tag">Retail</span>
              <h4>Multi-Branch Inventory System</h4>
              <p>Stock visibility and point-of-sale unification across a growing regional retail chain.</p>
              <div class="stack"><span>Laravel</span><span>Flutter</span><span>MySQL</span></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="testimonials" class="tight">
      <div class="wrap">
        <div class="section-head" data-reveal>
          <div class="eyebrow">// 06 &mdash; Client Voices</div>
          <h2>What partners say about working with us.</h2>
        </div>
        <div class="testimonials-mask" data-reveal>
          <div class="testi-track" id="testiTrack">
            <div class="testi-card">
              <div class="stars">★★★★★</div>
              <q>Navkwa understood our operations better than teams we&rsquo;d worked with for years. The system they built removed three manual processes in the first month.</q>
              <div class="testi-person"><div class="testi-avatar">O</div><div><div class="name">Operations Director</div><div class="role">Construction &amp; Real Estate Group</div></div></div>
            </div>
            <div class="testi-card">
              <div class="stars">★★★★★</div>
              <q>Communication was constant and honest. We always knew exactly what was being built and why &mdash; that&rsquo;s rare in software vendors.</q>
              <div class="testi-person"><div class="testi-avatar">F</div><div><div class="name">Finance Lead</div><div class="role">Regional Retail Chain</div></div></div>
            </div>
            <div class="testi-card">
              <div class="stars">★★★★★</div>
              <q>What impressed us most was the architecture. A year later, adding new features has been simple instead of a rebuild.</q>
              <div class="testi-person"><div class="testi-avatar">C</div><div><div class="name">Chief Technology Officer</div><div class="role">Logistics Startup</div></div></div>
            </div>
          </div>
        </div>
        <div class="testi-nav">
          <button id="testiPrev" aria-label="Previous"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg></button>
          <button id="testiNext" aria-label="Next"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg></button>
        </div>
        <p class="testimonials-note">Illustrative client feedback &mdash; case studies and references available on request.</p>
      </div>
    </section>

    <section id="stack">
      <div class="wrap">
        <div class="section-head" data-reveal>
          <div class="eyebrow">// 07 &mdash; Technology</div>
          <h2>The stack behind the systems.</h2>
        </div>
        <div class="tech-grid" data-reveal>
          <div class="tech-chip"><span class="tc-dot"></span>Next.js</div>
          <div class="tech-chip"><span class="tc-dot"></span>React</div>
          <div class="tech-chip"><span class="tc-dot"></span>Laravel</div>
          <div class="tech-chip"><span class="tc-dot"></span>PHP</div>
          <div class="tech-chip"><span class="tc-dot"></span>Node.js</div>
          <div class="tech-chip"><span class="tc-dot"></span>PostgreSQL</div>
          <div class="tech-chip"><span class="tc-dot"></span>Docker</div>
          <div class="tech-chip"><span class="tc-dot"></span>AWS</div>
          <div class="tech-chip"><span class="tc-dot"></span>Azure</div>
          <div class="tech-chip"><span class="tc-dot"></span>Cloudflare</div>
          <div class="tech-chip"><span class="tc-dot"></span>Flutter</div>
          <div class="tech-chip"><span class="tc-dot"></span>Kubernetes</div>
          <div class="tech-chip"><span class="tc-dot"></span>Redis</div>
          <div class="tech-chip"><span class="tc-dot"></span>OpenAI</div>
        </div>
      </div>
    </section>

    <section class="tight">
      <div class="wrap">
        <div class="cta-panel" data-reveal>
          <div class="eyebrow centered">// Ready when you are</div>
          <h2>Let&rsquo;s build the system your business is missing.</h2>
          <p>Book a free discovery call &mdash; we&rsquo;ll scope the problem, not just pitch a solution.</p>
          <div class="cta-actions">
            <a href="#contact" class="btn btn-primary btn-lg">Book Consultation</a>
            <a href="#contact" class="btn btn-ghost btn-lg">Send a Message</a>
          </div>
        </div>
      </div>
    </section>

    <section id="contact">
      <div class="wrap">
        <div class="section-head" data-reveal>
          <div class="eyebrow">// 08 &mdash; Contact</div>
          <h2>Tell us what you&rsquo;re building.</h2>
          <p>Three short steps. We&rsquo;ll come back to you within one business day with next steps.</p>
        </div>
        <div class="contact-grid">
          <div data-reveal>
            <div class="contact-info-item">
              <div class="ci-ic"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M4 4h16v16H4z"/><path d="M4 6l8 7 8-7"/></svg></div>
              <div><h5>Email</h5><p>contact@navkwagroup.com</p></div>
            </div>
            <div class="contact-info-item">
              <div class="ci-ic"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.8 19.8 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.8 19.8 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.36 1.9.68 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.9.32 1.85.55 2.81.68A2 2 0 0122 16.92z"/></svg></div>
              <div><h5>Phone &amp; WhatsApp</h5><p>+233 000 000 000</p></div>
            </div>
            <div class="contact-info-item">
              <div class="ci-ic"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
              <div><h5>Office</h5><p>Accra, Ghana</p></div>
            </div>
            <div class="contact-info-item">
              <div class="ci-ic"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></div>
              <div><h5>Live Chat</h5><p>Bottom-right, weekdays 8am&ndash;8pm GMT</p></div>
            </div>
          </div>

          <div class="form-card" data-reveal>
            <div class="steps-row">
              <div class="step-dot active" data-step-dot="1"></div>
              <div class="step-dot" data-step-dot="2"></div>
              <div class="step-dot" data-step-dot="3"></div>
            </div>

            <form id="discoveryForm" action="{{ route('contact.store') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <input type="hidden" name="service" value="Enterprise Software" data-chip-value="service">
              <input type="hidden" name="budget" value="Under $5,000" data-chip-value="budget">
              <input type="hidden" name="timeline" value="ASAP" data-chip-value="timeline">
              <div class="form-step active" data-step="1">
                <label class="field-label">Your name</label>
                <input class="field" name="name" type="text" placeholder="Ama Owusu" required>
                <div class="field-row">
                  <div><label class="field-label">Company</label><input class="field" name="company" type="text" placeholder="Company Ltd."></div>
                  <div><label class="field-label">Country</label><input class="field" name="country" type="text" placeholder="Ghana"></div>
                </div>
                <div class="field-row">
                  <div><label class="field-label">Email</label><input class="field" name="email" type="email" placeholder="you@company.com" required></div>
                  <div><label class="field-label">Phone</label><input class="field" name="phone" type="tel" placeholder="+233 ..."></div>
                </div>
                <div class="form-nav"><span></span><button type="button" class="btn btn-primary btn-sm" data-go-step="2">Next &rarr;</button></div>
              </div>

              <div class="form-step" data-step="2">
                <label class="field-label">Service interested in</label>
                <div class="chip-select" data-field="service">
                  <div class="chip-opt sel">Enterprise Software</div>
                  <div class="chip-opt">Web App</div>
                  <div class="chip-opt">Mobile App</div>
                  <div class="chip-opt">AI / Automation</div>
                  <div class="chip-opt">Not sure yet</div>
                </div>
                <label class="field-label">Budget range</label>
                <div class="chip-select" data-field="budget">
                  <div class="chip-opt sel">Under $5,000</div>
                  <div class="chip-opt">$5,000 &ndash; $20,000</div>
                  <div class="chip-opt">$20,000+</div>
                  <div class="chip-opt">Let&rsquo;s discuss</div>
                </div>
                <label class="field-label">Timeline</label>
                <div class="chip-select" data-field="timeline">
                  <div class="chip-opt sel">ASAP</div>
                  <div class="chip-opt">1&ndash;3 months</div>
                  <div class="chip-opt">3&ndash;6 months</div>
                  <div class="chip-opt">Just exploring</div>
                </div>
                <div class="form-nav"><button type="button" class="btn btn-ghost btn-sm" data-go-step="1">&larr; Back</button><button type="button" class="btn btn-primary btn-sm" data-go-step="3">Next &rarr;</button></div>
              </div>

              <div class="form-step" data-step="3">
                <label class="field-label">Tell us about the project</label>
                <textarea class="field" name="message" placeholder="What problem are you trying to solve?"></textarea>
                <label class="field-label">Attach a file (optional)</label>
                <input class="field" name="attachment" type="file">
                <div class="form-nav"><button type="button" class="btn btn-ghost btn-sm" data-go-step="2">&larr; Back</button><button type="submit" class="btn btn-primary btn-sm">Submit request</button></div>
              </div>
            </form>
            <p class="form-status" id="contactFormStatus" role="status"></p>

            <div class="success-box" id="successBox">
              <div class="success-check"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#06B6D4" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg></div>
              <h3 class="font-display success-title">Thank you.</h3>
              <p class="success-copy">Our team has received your request and will be in touch within one business day.</p>
            </div>
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
          <div class="social-row footer-social">
            <a href="#" aria-label="LinkedIn"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/><path d="M10 9v12M10 13a4 4 0 018 0v8"/></svg></a>
            <a href="#" aria-label="X"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M4 4l16 16M20 4L4 20"/></svg></a>
            <a href="#" aria-label="GitHub"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M12 2a10 10 0 00-3.16 19.5c.5.1.68-.22.68-.48v-1.7c-2.78.6-3.37-1.34-3.37-1.34-.46-1.16-1.1-1.47-1.1-1.47-.9-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.89 1.52 2.34 1.08 2.91.83.09-.65.35-1.08.63-1.33-2.22-.25-4.56-1.11-4.56-4.95 0-1.1.39-1.99 1.03-2.69-.1-.25-.45-1.27.1-2.65 0 0 .84-.27 2.75 1.02a9.5 9.5 0 015 0c1.91-1.3 2.75-1.02 2.75-1.02.55 1.38.2 2.4.1 2.65.64.7 1.03 1.59 1.03 2.69 0 3.85-2.34 4.7-4.57 4.94.36.31.68.92.68 1.85v2.74c0 .27.18.58.69.48A10 10 0 0012 2z"/></svg></a>
          </div>
        </div>
        <div>
          <h6>Company</h6>
          <ul><li><a href="#hero">About</a></li><li><a href="#work">Portfolio</a></li><li><a href="#">Careers</a></li><li><a href="#">Investors</a></li></ul>
        </div>
        <div>
          <h6>Services</h6>
          <ul><li><a href="#services">Custom Software</a></li><li><a href="#services">Web &amp; Mobile</a></li><li><a href="#services">Cloud &amp; AI</a></li><li><a href="#roadmap">Products</a></li></ul>
        </div>
        <div>
          <h6>Resources</h6>
          <ul><li><a href="#">Insights (Blog)</a></li><li><a href="#">Documentation</a></li><li><a href="#contact">Contact</a></li><li><a href="#">FAQ</a></li></ul>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2026 Navkwa Group Ltd. All rights reserved.</p>
        <p class="footer-links-inline"><a href="#">Privacy Policy</a><a href="#">Terms of Service</a></p>
      </div>
    </div>
  </footer>

  <button id="chat-launcher" aria-label="Open chat">
    <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
  </button>
  <div id="chat-panel">
    <div class="chat-head"><span class="chat-status-dot"></span><div><strong>Navkwa Support</strong><span>Typically replies in a few minutes</span></div></div>
    <div class="chat-body" id="chatBody">
      <div class="chat-bubble">Hi &#128075; - tell us a bit about your project and we&rsquo;ll point you to the right person.</div>
    </div>
    <form class="chat-foot" id="chatForm">
      <input id="chatInput" type="text" placeholder="Type a message..." autocomplete="off">
      <button aria-label="Send" type="submit">&rarr;</button>
    </form>
  </div>
</body>
</html>
