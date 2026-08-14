@php
  $navItems = [
    ['label' => 'About', 'route' => 'about.index', 'key' => 'about'],
    ['label' => 'Services', 'route' => 'services.index', 'key' => 'services'],
    ['label' => 'Products', 'route' => 'products.index', 'key' => 'products'],
    ['label' => 'Industries', 'route' => 'industries.index', 'key' => 'industries'],
    ['label' => 'Work', 'route' => 'work.index', 'key' => 'work'],
    ['label' => 'Contact', 'route' => 'contact.index', 'key' => 'contact'],
  ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $page['title'] }} - Navkwa Group Ltd.</title>
  <meta name="description" content="{{ $page['summary'] }}">
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
<body class="site-page {{ $page['slug'] }}-page {{ $page['slug'] !== 'about' ? 'site-page-no-hero' : '' }}">
  <div id="loader">
    <div class="mark font-display">NAVKWA</div>
    <div class="loader-bar"><span id="loaderFill"></span></div>
    <div class="loader-pct" id="loaderPct">00%</div>
  </div>

  <header id="siteHeader" class="scrolled">
    <div class="wrap">
      <nav>
        <a href="{{ route('home') }}" class="logo"><span class="dot"></span>Navkwa</a>
        <ul class="nav-links" id="navLinks">
          @foreach($navItems as $item)
            <li><a href="{{ route($item['route']) }}" class="{{ $activePage === $item['key'] ? 'active' : '' }}">{{ $item['label'] }}</a></li>
          @endforeach
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
    @if($page['slug'] === 'about')
      <section class="about-hero" data-page-headline="{{ $page['headline'] }}">
        <div class="about-hero-bg" aria-hidden="true"></div>
        @include('partials.hero-carousel-backdrop')
        @include('partials.hero-tech-field')
        <div class="wrap about-hero-wrap">
          <div class="about-hero-copy" data-reveal>
            <h1>An embodiment of young individuals disrupting the <span>Tech Ecosystem in Africa.</span></h1>
            <div class="about-hero-actions">
              <a href="{{ route('services.index') }}" class="btn btn-primary btn-lg">Explore Services</a>
              <a href="{{ route('work.index') }}" class="btn btn-ghost btn-lg">View Work</a>
            </div>
          </div>
        </div>
      </section>
    @endif

    @switch($page['slug'])
      @case('services')
        <section class="theme-light page-content-section" id="service-capabilities">
          <div class="wrap">
            <div class="section-head" data-reveal>
              <div class="eyebrow">// Service Capabilities</div>
              <h2>From product strategy to production software.</h2>
              <p>Navkwa builds software around how businesses actually operate, from early product planning to secure deployment.</p>
            </div>
            <div class="service-capability-showcase" data-reveal>
              <figure class="services-video-card">
                <video class="services-video" autoplay muted loop playsinline controls preload="metadata" aria-label="Navkwa services overview video">
                  <source src="{{ asset('assets/videos/services-overview.mp4') }}" type="video/mp4">
                  <source src="{{ asset('assets/videos/services-overview.mov') }}" type="video/quicktime">
                </video>
              </figure>
              <div class="services-grid services-grid-compact">
                <div class="service-card"><div><div class="si"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M4 4h16v16H4z"/><path d="M4 9h16"/></svg></div><h3>Custom Software Development</h3></div><p>Business systems, internal tools, client portals, CRM, ERP, and workflow platforms designed around your process.</p></div>
                <div class="service-card"><div><div class="si"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M12 20l-8-4V8l8-4 8 4v8z"/><path d="M12 12l8-4M12 12v8M12 12L4 8"/></svg></div><h3>SaaS Product Development</h3></div><p>Product architecture, subscriptions, user roles, dashboards, onboarding, and release planning for scalable SaaS platforms.</p></div>
                <div class="service-card"><div><div class="si"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><rect x="6" y="2" width="12" height="20" rx="2"/><path d="M11 18h2"/></svg></div><h3>Mobile Application Development</h3></div><p>Field-ready mobile apps for customers, teams, and operations that need reliable access beyond the office.</p></div>
                <div class="service-card"><div><div class="si"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M9 3v18M15 3v18M3 9h18M3 15h18"/></svg></div><h3>Cloud, API and Systems Integration</h3></div><p>Cloud deployment, third-party integrations, data pipelines, payment connections, and API-driven system interoperability.</p></div>
                <div class="service-card"><div><div class="si"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg></div><h3>AI and Business Automation</h3></div><p>Applied AI integrations, intelligent workflows, notifications, approvals, and automation for repetitive business tasks.</p></div>
                <div class="service-card"><div><div class="si"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M4 17V7l8-4 8 4v10l-8 4-8-4z"/><path d="M4 7l8 4 8-4M12 11v10"/></svg></div><h3>Product Design and Digital Transformation</h3></div><p>Process mapping, product strategy, UX design, interface planning, and digitisation for teams moving beyond spreadsheets.</p></div>
              </div>
            </div>
          </div>
        </section>
        <section class="theme-white">
          <div class="wrap">
            <div class="section-head" data-reveal>
              <div class="eyebrow">// Delivery Models</div>
              <h2>Choose the engagement model that fits the work.</h2>
              <p>We scope around the clarity of the problem, the urgency of delivery, and how quickly requirements are likely to change.</p>
            </div>
            <div class="engagement-grid" data-reveal>
              <article><h3>Fixed-Scope Project</h3><p>For clearly defined products, portals, workflows, and systems with agreed requirements and milestones.</p></article>
              <article><h3>Dedicated Product Team</h3><p>For ongoing development where the product evolves across multiple releases and changing business needs.</p></article>
              <article><h3>Support and Modernisation</h3><p>For existing systems that need improvement, integration, maintenance, documentation, or performance work.</p></article>
            </div>
          </div>
        </section>
        @break

      @case('products')
        <section class="theme-soft page-content-section">
          <div class="wrap">
            <div class="section-head" data-reveal>
              <div class="eyebrow">// Products</div>
              <h2>Available and active products.</h2>
              <p>Alongside client projects, we are developing focused platforms for industries where operational systems matter.</p>
            </div>
            <div class="build-erp-showcase" data-reveal>
              <article class="build-erp-copy">
                <span class="badge-product">Active Product &bull; Construction ERP</span>
                <h3>Navkwa Build</h3>
                <p class="build-erp-lede">The Construction Operating System for modern contractors. Plan projects, control costs, manage procurement, track site progress, automate approvals, and give every stakeholder real-time visibility from one connected platform.</p>
                <div class="build-erp-actions"><a href="{{ route('products.navkwa-build') }}" class="btn btn-primary btn-lg">Explore Full Product</a></div>
                <div class="build-erp-audience"><span>General Contractors</span><span>Real Estate Developers</span><span>Civil Engineering Firms</span><span>MEP Contractors</span></div>
              </article>
              <figure class="build-erp-photo">
                <div class="build-tablet-frame">
                  <span class="build-tablet-camera"></span>
                  <img src="{{ asset('assets/images/products/navkwa-build-erp-system.png') }}" alt="Navkwa Build construction ERP dashboard interface" loading="lazy" decoding="async">
                </div>
              </figure>
            </div>
            <div class="active-product-grid" data-reveal>
              <article class="fixam-product-card">
                <div class="fixam-product-copy">
                  <span class="badge-soon">In Development</span>
                  <h3>FixAm</h3>
                  <p>A service marketplace that helps customers discover and book verified artisans based on location, availability, and service category.</p>
                  <div class="stack"><span>Marketplace</span><span>Bookings</span><span>Artisan App</span><span>Location Search</span></div>
                </div>
              </article>
              <div class="fixam-phone-stage">
                <div class="fixam-phone-shell">
                  <span class="fixam-phone-notch"></span>
                  <img src="{{ asset('assets/images/products/fixam-app-screen.webp') }}" alt="FixAm mobile app screen" loading="lazy" decoding="async">
                </div>
              </div>
            </div>
            <div class="roadmap-next" data-reveal>
              <div class="section-head roadmap-head">
                <h2>The products we are building next.</h2>
                <p>Our roadmap focuses on operational systems for high-impact African industries.</p>
              </div>
            </div>
            <div class="roadmap-grid" data-reveal>
              <div class="roadmap-card"><span class="badge-soon">Roadmap</span><h3>Healthcare Operations Platform</h3><p>Patient workflows, appointments, records, billing, and operational reporting for clinics and health networks.</p></div>
              <div class="roadmap-card"><span class="badge-soon">Roadmap</span><h3>Workforce and Payroll Platform</h3><p>Employee records, leave, attendance, payroll preparation, and performance processes for growing teams.</p></div>
              <div class="roadmap-card"><span class="badge-soon">Roadmap</span><h3>Retail Inventory and POS</h3><p>Stock, sales, and multi-branch reporting for retailers who have outgrown spreadsheets.</p></div>
            </div>
          </div>
        </section>
        @break

      @case('industries')
        <section class="theme-white page-content-section" id="sector-focus">
          <div class="wrap">
            <div class="section-head" data-reveal>
              <div class="eyebrow">// Sector Focus</div>
              <h2>Software shaped by how each industry operates.</h2>
              <p>Every sector has different approvals, records, field workflows, reporting needs, and operational risks.</p>
            </div>
            <div class="industries-rail page-industries-grid" data-reveal>
              <div class="industry-tile industry-photo-card">
                <figure><img src="{{ asset('assets/images/industries/construction.png') }}" alt="Construction managers reviewing project information on a tablet at a building site" loading="lazy" decoding="async"></figure>
                <div class="industry-card-body"><h4>Construction</h4><span class="tag">Project, site, budget, and procurement control</span></div>
              </div>
              <div class="industry-tile industry-photo-card">
                <figure><img src="{{ asset('assets/images/industries/healthcare.png') }}" alt="Healthcare team reviewing patient operations on a tablet in a clinic" loading="lazy" decoding="async"></figure>
                <div class="industry-card-body"><h4>Healthcare</h4><span class="tag">Patient, records, billing, and clinic operations</span></div>
              </div>
              <div class="industry-tile industry-photo-card">
                <figure><img src="{{ asset('assets/images/industries/education.png') }}" alt="Education administrator guiding students through a digital learning workflow" loading="lazy" decoding="async"></figure>
                <div class="industry-card-body"><h4>Education</h4><span class="tag">Campus, learning, payments, and administration</span></div>
              </div>
              <div class="industry-tile industry-photo-card">
                <figure><img src="{{ asset('assets/images/industries/logistics.png') }}" alt="Logistics managers checking dispatch and warehouse operations on a tablet" loading="lazy" decoding="async"></figure>
                <div class="industry-card-body"><h4>Logistics</h4><span class="tag">Fleet, dispatch, tracking, and supply chain</span></div>
              </div>
              <div class="industry-tile industry-photo-card">
                <figure><img src="{{ asset('assets/images/industries/retail.png') }}" alt="Retail store team reviewing inventory and point of sale data on a tablet" loading="lazy" decoding="async"></figure>
                <div class="industry-card-body"><h4>Retail</h4><span class="tag">Inventory, POS, branches, and reporting</span></div>
              </div>
              <div class="industry-tile industry-photo-card">
                <figure><img src="{{ asset('assets/images/industries/public-sector.png') }}" alt="Public service officer helping a citizen in a modern digital service office" loading="lazy" decoding="async"></figure>
                <div class="industry-card-body"><h4>Public Sector</h4><span class="tag">Workflow systems, records, approvals, and service delivery</span></div>
              </div>
            </div>
          </div>
        </section>
        <section class="theme-soft">
          <div class="wrap">
            <div class="section-head" data-reveal>
              <div class="eyebrow">// Operating Clarity</div>
              <h2>We translate sector complexity into usable systems.</h2>
              <p>Discovery focuses on the actual workflow: who starts the process, who approves, what can go wrong, and what leadership needs to see.</p>
            </div>
            <div class="trust-grid" data-reveal>
              <span>Process mapping</span><span>Role permissions</span><span>Field reporting</span><span>Executive dashboards</span><span>Integration planning</span><span>Data governance</span><span>Branch operations</span><span>Support readiness</span>
            </div>
          </div>
        </section>
        @break

      @case('work')
        <section class="theme-white page-content-section">
          <div class="wrap">
            <div class="section-head" data-reveal>
              <div class="eyebrow">// Product Work</div>
              <h2>Active products and internal systems.</h2>
              <p>We show real product work and internal infrastructure transparently while public case studies are still being prepared.</p>
            </div>
            <div class="portfolio-scroll honest-work-grid" data-reveal>
              <div class="work-card"><div class="work-thumb work-thumb-build" role="img" aria-label="Navkwa Build construction site preview"></div><div class="work-body"><span class="wc-tag">Active Product</span><h4>Navkwa Build</h4><p>A construction operating system for contractors managing projects, procurement, budgets, teams, and approvals.</p><div class="stack"><span>Construction ERP</span><span>Subscription Platform</span><span>Dashboards</span></div></div></div>
              <div class="work-card"><div class="work-thumb work-thumb-fixam" role="img" aria-label="Customer booking a trusted artisan through FixAm"></div><div class="work-body"><span class="wc-tag">In Development</span><h4>FixAm</h4><p>A marketplace concept for customers to discover and book artisans by location, service category, and availability.</p><div class="stack"><span>Marketplace</span><span>Bookings</span><span>Location Search</span></div></div></div>
              <div class="work-card"><div class="work-thumb work-thumb-operations" role="img" aria-label="Navkwa team reviewing the operations portal dashboard"></div><div class="work-body"><span class="wc-tag">Internal System</span><h4>Navkwa Operations Portal</h4><p>An internal portal for enquiries, support conversations, content, and administrative workflows across the website.</p><div class="stack"><span>Laravel</span><span>Operations</span><span>Support Workflow</span></div></div></div>
            </div>
          </div>
        </section>
        <section class="theme-soft">
          <div class="wrap">
            <div class="section-head" data-reveal>
              <div class="eyebrow">// Delivery Evidence</div>
              <h2>How we present work before public case studies.</h2>
              <p>We avoid pretending internal concepts are client case studies. As delivered work becomes public, this page can grow into detailed case studies with outcomes, screenshots, and implementation notes.</p>
            </div>
            <div class="engagement-grid" data-reveal>
              <article><h3>Active Product</h3><p>Products that are being developed, positioned, priced, and prepared for commercial use.</p></article>
              <article><h3>Internal System</h3><p>Operational tools that support enquiries, support, payments, content, and business administration.</p></article>
              <article><h3>Future Case Studies</h3><p>Client stories will include the problem, delivery scope, system design, and measurable business outcome.</p></article>
            </div>
          </div>
        </section>
        @break

      @case('about')
        <section class="theme-white page-content-section navkwa-about">
          <div class="wrap">
            <div class="about-grid">
              <div class="section-head" data-reveal>
                <div class="eyebrow">// Company</div>
                <h2>A Ghanaian technology company building practical systems for African businesses.</h2>
              </div>
              <div class="about-copy" data-reveal>
                <p>Navkwa Group Ltd. designs and develops custom software, SaaS products, and digital platforms for businesses across Africa. We combine product strategy, engineering discipline, and operational understanding to turn complex processes into reliable systems.</p>
                <p>The technology arm is focused on building software that can support real business continuity: clear ownership, documented decisions, secure deployment, and post-launch improvement.</p>
              </div>
            </div>
            <figure class="about-photo-card" data-reveal>
              <img src="{{ asset('assets/images/about/client-satisfaction-meeting.jpg') }}" alt="Navkwa founder in a successful client satisfaction meeting">
              <figcaption class="trust-strip">
                <span>Ghana-based</span>
                <span>Africa-focused</span>
                <span>Secure-by-default delivery</span>
                <span>Documented handover</span>
                <span>Long-term support options</span>
              </figcaption>
            </figure>
          </div>
        </section>
        <section class="tight">
          <div class="wrap">
            <div class="why-layout">
              <div data-reveal>
                <div class="eyebrow">// Why Navkwa</div>
                <h2 class="font-display why-title">Built with the discipline critical systems demand.</h2>
                <p class="why-copy">Navkwa is designed for organisations that need thoughtful software, not template installs. We combine strategic product thinking with secure engineering and clear communication.</p>
              </div>
              <div class="why-list" data-reveal>
                <div class="why-item"><div><h4>Operational understanding</h4><p>We map the current workflow before writing code, so the system reflects how the business actually runs.</p></div></div>
                <div class="why-item"><div><h4>Secure architecture</h4><p>Authentication, permissions, backups, auditability, and data handling are treated as product requirements.</p></div></div>
                <div class="why-item"><div><h4>Transparent delivery</h4><p>Clear milestones, weekly progress updates, and documented decisions keep stakeholders aligned.</p></div></div>
                <div class="why-item"><div><h4>Post-launch thinking</h4><p>We plan maintenance, support, improvements, and handover before launch instead of treating them as afterthoughts.</p></div></div>
              </div>
            </div>
          </div>
        </section>
        <section class="theme-soft">
          <div class="wrap">
            <div class="section-head" data-reveal><div class="eyebrow">// Trust &amp; Governance</div><h2>Built for business continuity.</h2><p>Corporate clients need clarity on ownership, security, documentation, and support before they commit to software.</p></div>
            <div class="trust-grid" data-reveal><span>Confidentiality</span><span>Secure architecture</span><span>Role-based access</span><span>Documented handover</span><span>Automated backup planning</span><span>Source-code ownership terms</span><span>Data handling review</span><span>Support agreement options</span></div>
          </div>
        </section>
        @break

      @case('contact')
        <section class="theme-white page-content-section">
          <div class="wrap">
            <div class="section-head" data-reveal>
              <div class="eyebrow">// Contact Navkwa</div>
              <h2>Book a 30-minute discovery conversation.</h2>
              <p>Share your business problem, current process, and preferred next step. The Navkwa team reviews enquiries and responds within one business day.</p>
            </div>
            <div class="contact-grid" id="contact-message">
              <div data-reveal>
                <div class="contact-info-item"><div class="ci-ic"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M4 4h16v16H4z"/><path d="M4 6l8 7 8-7"/></svg></div><div><h5>Email</h5><p>{{ $siteSettings['company_email'] ?? 'info@navkwa.com' }}</p></div></div>
                <div class="contact-info-item"><div class="ci-ic"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.8 19.8 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.8 19.8 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.36 1.9.68 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.9.32 1.85.55 2.81.68A2 2 0 0122 16.92z"/></svg></div><div><h5>Phone &amp; WhatsApp</h5><p>{{ $siteSettings['company_phone'] ?? '+233553544198' }}</p></div></div>
                <div class="contact-info-item"><div class="ci-ic"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg></div><div><h5>Office</h5><p>{{ $siteSettings['office_address'] ?? 'Accra, Ghana' }}</p></div></div>
                <div class="contact-info-item"><div class="ci-ic"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></div><div><h5>Live Chat</h5><p>Bottom-right, {{ $siteSettings['business_hours'] ?? 'weekdays 8am-8pm GMT' }}</p></div></div>
              </div>
              <div class="form-card" data-reveal>
                <div class="steps-row"><div class="step-dot active" data-step-dot="1"></div><div class="step-dot" data-step-dot="2"></div><div class="step-dot" data-step-dot="3"></div></div>
                <form id="discoveryForm" action="{{ route('contact.store') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <input type="hidden" name="service" value="Custom Software Development" data-chip-value="service">
                  <input type="hidden" name="industry" value="Not specified" data-chip-value="industry">
                  <input type="hidden" name="budget" value="Under $5,000" data-chip-value="budget">
                  <input type="hidden" name="timeline" value="1-3 months" data-chip-value="timeline">
                  <input type="hidden" name="preferred_contact_method" value="Email" data-chip-value="preferred_contact_method">
                  <div class="form-step active" data-step="1">
                    <p class="form-step-label">Step 1 of 3</p>
                    <label class="field-label">Full name</label>
                    <input class="field" name="name" type="text" placeholder="Ama Owusu" required>
                    <div class="field-row"><div><label class="field-label">Work email</label><input class="field" name="email" type="email" placeholder="you@company.com" required></div><div><label class="field-label">Phone number</label><input class="field" name="phone" type="tel" placeholder="+233 ..."></div></div>
                    <div class="field-row"><div><label class="field-label">Company</label><input class="field" name="company" type="text" placeholder="Company Ltd."></div><div><label class="field-label">Country</label><input class="field" name="country" type="text" placeholder="Ghana"></div></div>
                    <div class="form-nav"><span></span><button type="button" class="btn btn-primary btn-sm" data-go-step="2">Continue &rarr;</button></div>
                  </div>
                  <div class="form-step" data-step="2">
                    <p class="form-step-label">Step 2 of 3</p>
                    <label class="field-label">Service required</label>
                    <div class="chip-select" data-field="service"><div class="chip-opt sel">Custom Software Development</div><div class="chip-opt">Mobile Application Development</div><div class="chip-opt">SaaS Product Development</div><div class="chip-opt">AI and Business Automation</div><div class="chip-opt">Cloud, API and Systems Integration</div><div class="chip-opt">Not sure yet</div></div>
                    <label class="field-label">Industry</label>
                    <div class="chip-select" data-field="industry"><div class="chip-opt">Construction</div><div class="chip-opt">Healthcare</div><div class="chip-opt">Education</div><div class="chip-opt">Logistics</div><div class="chip-opt">Retail</div><div class="chip-opt sel">Not specified</div></div>
                    <label class="field-label">What problem are you trying to solve?</label>
                    <textarea class="field" name="message" placeholder="Describe the workflow, product, or system you want to improve."></textarea>
                    <label class="field-label">Estimated budget range</label>
                    <div class="chip-select" data-field="budget"><div class="chip-opt sel">Under $5,000</div><div class="chip-opt">$5,000 &ndash; $20,000</div><div class="chip-opt">$20,000+</div><div class="chip-opt">Let us discuss</div></div>
                    <label class="field-label">Preferred timeline</label>
                    <div class="chip-select" data-field="timeline"><div class="chip-opt">ASAP</div><div class="chip-opt sel">1&ndash;3 months</div><div class="chip-opt">3&ndash;6 months</div><div class="chip-opt">Just exploring</div></div>
                    <label class="field-label">Existing system or process</label>
                    <input class="field" name="existing_system" type="text" placeholder="Excel, WhatsApp, legacy software, paper process...">
                    <label class="field-label">Existing system, document, or brief (optional)</label>
                    <input class="field" name="attachment" type="file">
                    <div class="form-nav"><button type="button" class="btn btn-ghost btn-sm" data-go-step="1">&larr; Back</button><button type="button" class="btn btn-primary btn-sm" data-go-step="3">Continue &rarr;</button></div>
                  </div>
                  <div class="form-step" data-step="3">
                    <p class="form-step-label">Step 3 of 3</p>
                    <label class="field-label">Preferred contact method</label>
                    <div class="chip-select" data-field="preferred_contact_method"><div class="chip-opt sel">Email</div><div class="chip-opt">Phone call</div><div class="chip-opt">WhatsApp</div></div>
                    <label class="consent-row"><input type="checkbox" required> <span>I consent to Navkwa contacting me about this enquiry.</span></label>
                    <div class="form-nav"><button type="button" class="btn btn-ghost btn-sm" data-go-step="2">&larr; Back</button><button type="submit" class="btn btn-primary btn-sm">Submit request</button></div>
                  </div>
                </form>
                <p class="form-status" id="contactFormStatus" role="status"></p>
                <div class="success-box" id="successBox"><div class="success-check"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg></div><h3 class="font-display success-title">Thank you.</h3><p class="success-copy">Our team has received your request and will be in touch within one business day.</p></div>
              </div>
            </div>
          </div>
        </section>
        @break
    @endswitch
  </main>

  <footer>
    <div class="wrap">
      <div class="footer-grid">
        <div>
          <a href="{{ route('home') }}" class="logo footer-brand"><span class="dot"></span>Navkwa</a>
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
        <p class="footer-links-inline"><a href="{{ route('home') }}">Home</a><a href="{{ route('payments.create') }}">Payments</a><a href="{{ route('legal.privacy') }}">Privacy Policy</a><a href="{{ route('legal.terms') }}">Terms of Use</a><a href="{{ route('legal.cookies') }}">Cookie Policy</a></p>
      </div>
    </div>
  </footer>

  <button id="chat-launcher" aria-label="Open chat">
    <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
  </button>
  <div id="chat-panel">
    <div class="chat-head"><span class="chat-status-dot"></span><div><strong>Navkwa Support</strong><span>Replies during business hours</span></div></div>
    <div class="chat-body" id="chatBody">
      <div class="chat-bubble">Hi - tell us a bit about your project and we will point you to the right person.</div>
    </div>
    <p class="chat-status-line" id="chatStatus" role="status"></p>
    <form class="chat-foot" id="chatForm">
      <input id="chatInput" type="text" placeholder="Type a message..." autocomplete="off" maxlength="2000" required>
      <button aria-label="Send" type="submit">&rarr;</button>
    </form>
  </div>
</body>
</html>
