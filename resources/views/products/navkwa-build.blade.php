<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Navkwa Build - Construction Operating System for Modern Contractors</title>
  <meta name="description" content="Navkwa Build is a construction operating system and ERP platform for contractors, developers, engineering firms, and infrastructure teams managing projects, costs, procurement, site progress, approvals, and reporting.">
  <meta name="keywords" content="construction ERP, construction operating system, contractor software, construction management software, project cost control, procurement management, site reporting, Navkwa Build, Ghana construction software">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="canonical" href="{{ url('/products/navkwa-build') }}">
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
  <meta property="og:title" content="Navkwa Build - Construction Operating System">
  <meta property="og:description" content="Plan projects, control costs, manage procurement, track site progress, automate approvals, and give stakeholders real-time visibility from one connected platform.">
  <meta property="og:type" content="website">
  <meta property="og:url" content="{{ url('/products/navkwa-build') }}">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">

  <script type="application/ld+json">
  {
    "@@context": "https://schema.org",
    "@@type": "SoftwareApplication",
    "name": "Navkwa Build",
    "applicationCategory": "BusinessApplication",
    "operatingSystem": "Web",
    "description": "Construction operating system and ERP software for managing projects, budgets, procurement, sites, approvals, and executive reporting.",
    "brand": {
      "@@type": "Brand",
      "name": "Navkwa"
    }
  }
  </script>
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
<body class="product-page">
  <header id="siteHeader">
    <div class="wrap">
      <nav>
        <a href="{{ route('home') }}" class="logo"><span class="dot"></span>Navkwa</a>
        <ul class="nav-links" id="navLinks">
          <li><a href="#overview">Product</a></li>
          <li><a href="#features">Features</a></li>
          <li><a href="#product-tour">Tour</a></li>
          <li><a href="#pricing">Pricing</a></li>
          <li><a href="#docs">Docs</a></li>
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
    <section id="overview" class="build-product-intro theme-light">
      <div class="wrap">
        <div class="build-product-intro-grid">
          <div class="build-product-intro-copy" data-reveal>
            <div class="eyebrow font-mono">// Navkwa Build</div>
            <h1>The Construction Operating System for Modern Contractors.</h1>
            <p class="lead">Plan projects, control costs, manage procurement, track site progress, automate approvals, and give every stakeholder real-time visibility&mdash;all from one connected platform.</p>
            <div class="build-product-intro-actions">
              <a href="#demo" class="btn btn-primary btn-lg">Request Demo</a>
              <a href="#product-tour" class="btn btn-ghost btn-lg">Watch Product Tour</a>
            </div>
          </div>
          <aside class="build-product-intro-note" data-reveal>
            <span>Field to finance visibility</span>
            <strong>Real construction operations, connected in one platform.</strong>
          </aside>
        </div>
      </div>
    </section>

    <section class="build-product-info theme-light">
      <div class="wrap">
        <div class="section-head" data-reveal>
          <div class="eyebrow">// Product Information</div>
          <h2>One operating system for projects, cost, procurement, site teams, and leadership.</h2>
          <p>Navkwa Build replaces disconnected spreadsheets, WhatsApp threads, paper site reports, and delayed finance updates with one shared construction ERP workspace.</p>
        </div>
        <div class="build-info-grid" data-reveal>
          <div>
            <h3>For day-to-day delivery</h3>
            <p>Project managers and site supervisors can track milestones, daily reports, RFIs, site diaries, approvals, workforce activity, and issues across every active job.</p>
          </div>
          <div>
            <h3>For financial control</h3>
            <p>Quantity surveyors and finance teams can monitor budgets, cost codes, commitments, payment certificates, invoices, cash flow, and variation orders.</p>
          </div>
          <div>
            <h3>For executive decisions</h3>
            <p>Directors get live visibility into revenue, profitability, delayed tasks, inventory exposure, supplier performance, outstanding approvals, and project risk.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="industries" class="theme-white">
      <div class="wrap">
        <div class="section-head" data-reveal>
          <div class="eyebrow">// Industries</div>
          <h2>Built for every construction discipline.</h2>
          <p>Whether your work is vertical construction, infrastructure, civil engineering, or EPC delivery, Navkwa Build adapts around your company structure.</p>
        </div>
        <div class="industry-check-grid" data-reveal>
          <span>Building Construction</span>
          <span>Civil Engineering</span>
          <span>Roads &amp; Highways</span>
          <span>Water Projects</span>
          <span>Commercial Developments</span>
          <span>Residential Developers</span>
          <span>Mining Contractors</span>
          <span>EPC Contractors</span>
        </div>
      </div>
    </section>

    <section id="features">
      <div class="wrap">
        <div class="section-head" data-reveal>
          <div class="eyebrow">// Features</div>
          <h2>A full construction ERP, grouped around how work actually happens.</h2>
          <p>Each module can be configured to your approval chains, project roles, cost structure, suppliers, and reporting requirements.</p>
        </div>
        <div class="module-grid" data-reveal>
          <article>
            <span>Project Delivery</span>
            <h3>Plan, coordinate, and report site progress.</h3>
            <ul><li>Project Planning</li><li>Daily Reports</li><li>Progress Tracking</li><li>Milestones</li><li>RFIs</li><li>Site Diaries</li></ul>
          </article>
          <article>
            <span>Finance</span>
            <h3>Control budget exposure before it becomes a problem.</h3>
            <ul><li>Budgets</li><li>Cost Codes</li><li>Variation Orders</li><li>Invoicing</li><li>Cash Flow</li><li>Payment Certificates</li></ul>
          </article>
          <article>
            <span>Procurement</span>
            <h3>Connect requests, suppliers, purchase orders, and inventory.</h3>
            <ul><li>Purchase Requests</li><li>Purchase Orders</li><li>Vendor Management</li><li>Inventory</li><li>Stock Transfers</li></ul>
          </article>
          <article>
            <span>Workforce</span>
            <h3>Know who is on site and how labour affects cost.</h3>
            <ul><li>Attendance</li><li>Labour Cost</li><li>Payroll Integration</li><li>Subcontractors</li><li>Crew Assignments</li></ul>
          </article>
          <article>
            <span>Executive Intelligence</span>
            <h3>Give leadership a clean view of project health.</h3>
            <ul><li>Live Dashboards</li><li>KPIs</li><li>Reports</li><li>Profitability</li><li>Forecasting</li></ul>
          </article>
          <article>
            <span>Approvals &amp; Documents</span>
            <h3>Keep decisions traceable and documents findable.</h3>
            <ul><li>Approval Chains</li><li>Audit Trails</li><li>PDF Drawings</li><li>Contracts</li><li>Change Requests</li></ul>
          </article>
        </div>
      </div>
    </section>

    <section id="product-tour" class="product-tour-section">
      <div class="wrap">
        <div class="section-head" data-reveal>
          <div class="eyebrow">// Product Tour</div>
          <h2>Explore the ERP before requesting a demo.</h2>
          <p>Switch between operating views to see how different teams experience Navkwa Build.</p>
        </div>
        <div class="tour-layout" data-reveal>
          <div class="tour-controls" aria-label="Product tour controls">
            <button type="button" class="active" data-product-panel-trigger="projects">Projects Dashboard</button>
            <button type="button" data-product-panel-trigger="finance">Finance Dashboard</button>
            <button type="button" data-product-panel-trigger="procurement">Procurement</button>
            <button type="button" data-product-panel-trigger="mobile">Mobile App</button>
          </div>
          <div class="tour-screen-wrap">
            <article class="product-shot active" data-product-panel="projects">
              <div class="shot-header"><span>Projects</span><strong>Portfolio command board</strong></div>
              <div class="shot-grid">
                <div><small>Active projects</small><strong>24</strong></div>
                <div><small>At risk</small><strong>3</strong></div>
                <div><small>Milestones this week</small><strong>42</strong></div>
              </div>
              <div class="shot-board"><span style="--fill:88%">Airport Road Mixed-Use</span><span style="--fill:71%">Tema Warehouse Phase II</span><span style="--fill:56%">Kumasi Roadworks</span></div>
            </article>
            <article class="product-shot" data-product-panel="finance">
              <div class="shot-header"><span>Finance</span><strong>Cost and margin control</strong></div>
              <div class="shot-grid">
                <div><small>Revenue</small><strong>GH₵48.2M</strong></div>
                <div><small>Margin</small><strong>18.4%</strong></div>
                <div><small>Outstanding</small><strong>GH₵1.8M</strong></div>
              </div>
              <div class="shot-table"><div><span>Variation order</span><strong>Pending QS review</strong></div><div><span>Payment certificate</span><strong>Approved</strong></div><div><span>Cost overrun alert</span><strong>MEP package</strong></div></div>
            </article>
            <article class="product-shot" data-product-panel="procurement">
              <div class="shot-header"><span>Procurement</span><strong>Supplier and inventory pipeline</strong></div>
              <div class="shot-grid">
                <div><small>POs open</small><strong>38</strong></div>
                <div><small>Inventory alerts</small><strong>9</strong></div>
                <div><small>Suppliers active</small><strong>64</strong></div>
              </div>
              <div class="shot-table"><div><span>Steel rods</span><strong>Delivery tomorrow</strong></div><div><span>Cement stock</span><strong>Below threshold</strong></div><div><span>Tiles supplier</span><strong>Awaiting approval</strong></div></div>
            </article>
            <article class="product-shot mobile-shot" data-product-panel="mobile">
              <div class="shot-header"><span>Mobile</span><strong>Site reporting in the field</strong></div>
              <div class="mobile-frame">
                <div><small>Workers on site</small><strong>316</strong></div>
                <div><small>Daily report</small><strong>Submitted</strong></div>
                <div><small>Photos uploaded</small><strong>28</strong></div>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>

    <section id="integrations" class="theme-soft">
      <div class="wrap">
        <div class="section-head" data-reveal>
          <div class="eyebrow">// Integrations</div>
          <h2>Works with the tools construction teams already use.</h2>
          <p>Integrations can be enabled or scoped during implementation depending on your current workflow.</p>
        </div>
        <div class="integration-grid" data-reveal>
          <span>Microsoft Excel</span><span>Power BI</span><span>AutoCAD</span><span>Email</span><span>WhatsApp Notifications</span><span>Google Maps</span><span>Microsoft Project</span><span>PDF Drawings</span><span>CSV Import</span><span>API</span>
        </div>
      </div>
    </section>

    <section id="security" class="security-section">
      <div class="wrap">
        <div class="section-head" data-reveal>
          <div class="eyebrow">// Enterprise Security</div>
          <h2>Built for executive confidence and operational accountability.</h2>
          <p>Security, access control, backups, and auditability are treated as core product requirements, not add-ons.</p>
        </div>
        <div class="security-grid" data-reveal>
          <article><h3>Role-Based Permissions</h3><p>Control exactly what directors, project managers, QS teams, finance, procurement, and site staff can access.</p></article>
          <article><h3>Encrypted Connections</h3><p>Protect sensitive commercial, project, supplier, and financial information in transit.</p></article>
          <article><h3>Audit Logs</h3><p>Track approvals, data changes, document updates, and activity across critical workflows.</p></article>
          <article><h3>Two-Factor Authentication</h3><p>Add stronger login protection for admin, finance, and executive accounts.</p></article>
          <article><h3>Activity Tracking</h3><p>Monitor user actions across modules and see who changed what, when, and where.</p></article>
          <article><h3>Automatic Backups</h3><p>Daily backups and restore planning help protect operational continuity.</p></article>
        </div>
      </div>
    </section>

    <section id="implementation" class="theme-white">
      <div class="wrap">
        <div class="section-head" data-reveal>
          <div class="eyebrow">// Implementation</div>
          <h2>A clear rollout plan from discovery to go-live.</h2>
          <p>We reduce uncertainty by aligning the product setup around your data, people, approvals, and reporting needs before launch.</p>
        </div>
        <div class="timeline-steps" data-reveal>
          <div><strong>Discovery</strong></div>
          <div><strong>Configuration</strong></div>
          <div><strong>Data Migration</strong></div>
          <div><strong>Training</strong></div>
          <div><strong>Launch &amp; Support</strong></div>
        </div>
      </div>
    </section>

    <section id="outcomes">
      <div class="wrap">
        <div class="section-head" data-reveal>
          <div class="eyebrow">// Customer Success</div>
          <h2>Why companies choose Navkwa Build.</h2>
          <p>Construction companies adopt ERP when the old reporting rhythm stops keeping up with their project complexity.</p>
        </div>
        <div class="outcome-grid" data-reveal>
          <span>Reduce reporting time</span>
          <span>Eliminate spreadsheet duplication</span>
          <span>Faster approvals</span>
          <span>Better budget visibility</span>
          <span>Centralize project information</span>
          <span>Improve executive decision-making</span>
        </div>
      </div>
    </section>

    <section id="pricing" class="build-pricing theme-soft">
      <div class="wrap">
        <div class="section-head" data-reveal>
          <div class="eyebrow">// Pricing</div>
          <h2>Navkwa Build Pricing</h2>
          <p>One construction operating system with features unlocked by subscription tier. Every plan supports unlimited projects, then scales by users, storage, advanced capabilities, support level, and enterprise services.</p>
        </div>
        <div class="pricing-action-grid" data-reveal>
          <div class="billing-note">
            <strong>Monthly or annual billing</strong>
            <span>Annual subscriptions include two months free.</span>
          </div>
          <div class="build-payment-entry">
            <div>
              <strong>Start Navkwa Build payment</strong>
              <span>Open the payment details page for the recommended Professional plan, or choose a different plan below.</span>
            </div>
            <a href="{{ route('products.navkwa-build.payment', ['plan' => 'professional']) }}" class="btn btn-primary btn-sm">Start Payment</a>
          </div>
        </div>
        <div class="pricing-grid subscription-pricing-grid" data-reveal>
          <article class="pricing-card">
            <div class="plan-card-top"><span class="plan-label">Essential</span></div>
            <h3>For small contractors and growing construction businesses.</h3>
            <p class="plan-price">GH₵399<span>/month</span></p>
            <div class="plan-summary">
              <p><strong>Who is it for?</strong> Small contractors, residential builders, subcontractors, and companies managing a few projects.</p>
              <p><strong>What does it solve?</strong> Centralizes project records, tasks, budgets, purchase requests, daily reports, and site documentation.</p>
              <p><strong>Expected outcomes</strong> Better project visibility, fewer lost updates, cleaner budget tracking, and faster reporting from the field.</p>
            </div>
            <details class="plan-inclusions">
              <summary>Included modules</summary>
              <div class="plan-feature-groups">
                <div><h4>Core Platform</h4><ul><li>Company Workspace</li><li>User Management</li><li>Projects</li><li>Tasks</li><li>Project Timeline</li><li>Daily Site Reports</li><li>Document Storage</li><li>Photo Uploads</li><li>Project Dashboard</li></ul></div>
                <div><h4>Financial Control</h4><ul><li>Project Budgets</li><li>Budget Tracking</li><li>Basic Cost Codes</li><li>Expense Tracking</li></ul></div>
                <div><h4>Procurement</h4><ul><li>Purchase Requests</li><li>Purchase Orders</li><li>Supplier Directory</li></ul></div>
                <div><h4>Reporting, Mobile &amp; Support</h4><ul><li>Basic Reports</li><li>Project Status Reports</li><li>Mobile Responsive</li><li>Field Reporting</li><li>Email Support</li><li>Knowledge Base</li></ul></div>
              </div>
            </details>
            <a href="{{ route('products.navkwa-build.payment', ['plan' => 'essential']) }}" class="btn btn-ghost btn-sm">Start with Essential</a>
          </article>
          <article class="pricing-card featured-plan">
            <div class="plan-card-top"><span class="plan-label">Professional</span><span class="plan-badge">Recommended</span></div>
            <h3>Everything in Essential, plus tools for companies managing multiple projects simultaneously.</h3>
            <p class="plan-price">GH₵999<span>/month</span></p>
            <div class="plan-summary">
              <p><strong>Who is it for?</strong> Contractors coordinating several active sites, branches, supervisors, suppliers, and approval chains.</p>
              <p><strong>What does it solve?</strong> Improves control over RFIs, drawings, procurement, inventory, invoicing, labour attendance, and executive dashboards.</p>
              <p><strong>Expected outcomes</strong> Faster approvals, better cost control, stronger procurement visibility, and less duplication across teams.</p>
            </div>
            <details class="plan-inclusions">
              <summary>Included modules</summary>
              <div class="plan-feature-groups">
                <div><h4>Advanced Project Management</h4><ul><li>Multiple Branches</li><li>Multiple Active Projects</li><li>RFIs</li><li>Submittals</li><li>Drawing Management</li><li>Approval Workflows</li><li>Change Orders</li></ul></div>
                <div><h4>Procurement</h4><ul><li>Inventory</li><li>Warehouses</li><li>Material Transfers</li><li>Delivery Tracking</li><li>Supplier Performance</li></ul></div>
                <div><h4>Finance &amp; Workforce</h4><ul><li>Invoicing</li><li>Payment Tracking</li><li>Client Billing</li><li>Cost Variance</li><li>Profitability</li><li>Labour Attendance</li><li>Crew Management</li><li>Timesheets</li></ul></div>
                <div><h4>Dashboards, Automation &amp; Support</h4><ul><li>Executive Dashboard</li><li>Procurement Dashboard</li><li>Financial Dashboard</li><li>Email Notifications</li><li>Automated Reminders</li><li>Approval Notifications</li><li>Priority Support</li></ul></div>
              </div>
            </details>
            <a href="{{ route('products.navkwa-build.payment', ['plan' => 'professional']) }}" class="btn btn-primary btn-sm">Choose Professional</a>
          </article>
          <article class="pricing-card">
            <div class="plan-card-top"><span class="plan-label">Business</span></div>
            <h3>Everything in Professional, designed for established construction companies.</h3>
            <p class="plan-price">GH₵2,499<span>/month</span></p>
            <div class="plan-summary">
              <p><strong>Who is it for?</strong> Established contractors that need sales, tendering, HR, QA/HSE, portals, equipment, analytics, and API access.</p>
              <p><strong>What does it solve?</strong> Connects pre-construction, delivery, finance, workforce, safety, equipment, clients, and consultants in one platform.</p>
              <p><strong>Expected outcomes</strong> Better tender control, stronger executive intelligence, improved compliance, and higher profitability visibility.</p>
            </div>
            <details class="plan-inclusions">
              <summary>Included modules</summary>
              <div class="plan-feature-groups">
                <div><h4>CRM &amp; Tendering</h4><ul><li>Lead Management</li><li>Opportunities</li><li>Clients</li><li>Tender Register</li><li>BOQs</li><li>Estimates</li><li>RFIs</li><li>Bid Tracking</li></ul></div>
                <div><h4>HR, QA/HSE &amp; Portals</h4><ul><li>Employees</li><li>Departments</li><li>Leave</li><li>Payroll Preparation</li><li>Inspections</li><li>NCR</li><li>Safety Incidents</li><li>Corrective Actions</li><li>Client Portal</li><li>Consultant Portal</li><li>Drawing Reviews</li></ul></div>
                <div><h4>Equipment</h4><ul><li>Equipment Register</li><li>Maintenance</li><li>Utilization</li></ul></div>
                <div><h4>Business Intelligence &amp; API</h4><ul><li>Advanced Reports</li><li>KPI Dashboard</li><li>Cost Analytics</li><li>Profit Analysis</li><li>REST API</li><li>Webhooks</li></ul></div>
              </div>
            </details>
            <a href="{{ route('products.navkwa-build.payment', ['plan' => 'business']) }}" class="btn btn-ghost btn-sm">Choose Business</a>
          </article>
          <article class="pricing-card enterprise-plan">
            <div class="plan-card-top"><span class="plan-label">Enterprise</span><span class="plan-badge">Custom</span></div>
            <h3>Everything in Business, for national contractors and enterprise organizations.</h3>
            <p class="plan-price">Custom<span> quotation</span></p>
            <div class="plan-summary">
              <p><strong>Who is it for?</strong> National contractors, enterprise groups, multi-country operators, and organizations with complex IT requirements.</p>
              <p><strong>What does it solve?</strong> Supports multi-company operations, advanced security, AI insight, integrations, enterprise services, and governed rollout.</p>
              <p><strong>Expected outcomes</strong> Stronger governance, forecasting, risk detection, system integration, and enterprise-grade operational control.</p>
            </div>
            <details class="plan-inclusions">
              <summary>Included modules</summary>
              <div class="plan-feature-groups">
                <div><h4>Enterprise Features</h4><ul><li>Unlimited Branches</li><li>Unlimited Companies</li><li>Multi-country</li><li>Multiple Currencies</li><li>Custom Approval Workflows</li><li>SSO</li><li>Active Directory</li><li>Audit Logs</li><li>Advanced Permissions</li></ul></div>
                <div><h4>AI</h4><ul><li>Cost Forecasting</li><li>Project Risk Detection</li><li>Procurement Recommendations</li><li>Equipment Analytics</li></ul></div>
                <div><h4>Integrations</h4><ul><li>Accounting Systems</li><li>BI Platforms</li><li>Government Systems</li><li>Custom APIs</li></ul></div>
                <div><h4>Enterprise Services</h4><ul><li>Dedicated Account Manager</li><li>Implementation Team</li><li>Data Migration</li><li>Staff Training</li><li>SLA</li><li>24/7 Support</li></ul></div>
              </div>
            </details>
            <a href="#demo" class="btn btn-ghost btn-sm">Request Enterprise Quote</a>
          </article>
        </div>
        <div class="pricing-guidance" data-reveal>
          <h3>We do not limit you by project count.</h3>
          <p>Construction companies often manage many live projects, so Navkwa Build scales by users, storage, advanced capabilities, support level, and enterprise services instead of restricting the number of projects your team can run.</p>
        </div>
        <div class="feature-comparison" data-reveal>
          <div class="comparison-head">
            <h3>Feature Comparison</h3>
            <p>See how the platform unlocks more advanced operating capabilities as your company grows.</p>
          </div>
          <div class="comparison-table-wrap">
            <table class="comparison-table">
              <thead>
                <tr><th>Feature</th><th>Essential</th><th>Professional</th><th>Business</th><th>Enterprise</th></tr>
              </thead>
              <tbody>
                <tr><td>Projects</td><td>Yes</td><td>Yes</td><td>Yes</td><td>Yes</td></tr>
                <tr><td>Budgets</td><td>Yes</td><td>Yes</td><td>Yes</td><td>Yes</td></tr>
                <tr><td>Procurement</td><td>Yes</td><td>Yes</td><td>Yes</td><td>Yes</td></tr>
                <tr><td>Inventory</td><td>-</td><td>Yes</td><td>Yes</td><td>Yes</td></tr>
                <tr><td>Finance</td><td>Basic</td><td>Advanced</td><td>Advanced</td><td>Enterprise</td></tr>
                <tr><td>CRM</td><td>-</td><td>-</td><td>Yes</td><td>Yes</td></tr>
                <tr><td>Tendering</td><td>-</td><td>-</td><td>Yes</td><td>Yes</td></tr>
                <tr><td>HR</td><td>-</td><td>-</td><td>Yes</td><td>Yes</td></tr>
                <tr><td>QA/HSE</td><td>-</td><td>-</td><td>Yes</td><td>Yes</td></tr>
                <tr><td>Client Portal</td><td>-</td><td>-</td><td>Yes</td><td>Yes</td></tr>
                <tr><td>Equipment</td><td>-</td><td>-</td><td>Yes</td><td>Yes</td></tr>
                <tr><td>API</td><td>-</td><td>-</td><td>Yes</td><td>Yes</td></tr>
                <tr><td>AI</td><td>-</td><td>-</td><td>-</td><td>Yes</td></tr>
                <tr><td>Multi-country</td><td>-</td><td>-</td><td>-</td><td>Yes</td></tr>
                <tr><td>SSO</td><td>-</td><td>-</td><td>-</td><td>Yes</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

    <section id="docs" class="theme-white">
      <div class="wrap">
        <div class="section-head" data-reveal>
          <div class="eyebrow">// Blog &amp; Documentation</div>
          <h2>Resources for construction teams evaluating ERP.</h2>
          <p>Use these guides to prepare your team, your data, and your rollout plan before implementation begins.</p>
        </div>
        <div class="resource-grid" data-reveal>
          <article>
            <span>Guide</span>
            <h3>How to choose construction ERP software</h3>
            <p>What contractors should compare across project controls, budgets, procurement, reporting, mobile access, and support.</p>
            <a href="#demo">Ask for the guide</a>
          </article>
          <article>
            <span>Documentation</span>
            <h3>Navkwa Build implementation checklist</h3>
            <p>The data, roles, workflows, and approval steps we review before configuring your construction ERP workspace.</p>
            <a href="#demo">Request documentation</a>
          </article>
          <article>
            <span>SEO Brief</span>
            <h3>Construction ERP for African contractors</h3>
            <p>Why modern construction companies need connected software for materials, site reports, cost control, and delivery visibility.</p>
            <a href="#seo-content">Read overview</a>
          </article>
        </div>
      </div>
    </section>

    <section id="seo-content" class="seo-copy-section">
      <div class="wrap">
        <div class="seo-copy" data-reveal>
          <h2>Construction ERP software for project visibility, budget control, and better site coordination.</h2>
          <p>Navkwa Build is construction management software for companies that need a clearer operating system for project delivery. It helps contractors replace disconnected spreadsheets and manual reporting with a centralized ERP platform for project managers, quantity surveyors, site supervisors, procurement teams, finance teams, and executives.</p>
          <p>For construction companies in Ghana and across Africa, Navkwa Build supports day-to-day control over budgets, procurement, materials, labour, approvals, documents, and progress reporting. The goal is simple: give leadership real-time visibility while giving site teams practical tools they can use every week.</p>
        </div>
      </div>
    </section>

    <section id="faq" class="theme-soft">
      <div class="wrap">
        <div class="section-head" data-reveal>
          <div class="eyebrow">// FAQ</div>
          <h2>Common questions from construction teams.</h2>
        </div>
        <div class="faq-grid" data-reveal>
          <details open><summary>How long does implementation take?</summary><p>A focused rollout can begin in four weeks. Larger multi-branch deployments depend on data migration, integrations, approval workflows, and training needs.</p></details>
          <details><summary>Can we migrate from Excel?</summary><p>Yes. We can import structured Excel or CSV data for projects, budgets, suppliers, cost codes, inventory, and contacts.</p></details>
          <details><summary>Can multiple branches use it?</summary><p>Yes. Navkwa Build can support multiple branches, project locations, teams, and permission groups.</p></details>
          <details><summary>Does it work on mobile?</summary><p>The platform is designed for site-friendly workflows, including mobile access for daily reports, attendance, progress updates, and approvals.</p></details>
          <details><summary>Can approvals be customized?</summary><p>Yes. Purchase requests, payment certificates, variations, RFIs, and other workflows can follow your approval chain.</p></details>
          <details><summary>Can we self-host?</summary><p>Deployment options can be discussed for enterprise clients with specific hosting, compliance, or IT requirements.</p></details>
          <details><summary>How are backups handled?</summary><p>Backup policies are configured during rollout, with daily backup planning available for production deployments.</p></details>
          <details><summary>Can it integrate with accounting software?</summary><p>Yes. Accounting integrations can be scoped during implementation, along with exports for finance teams.</p></details>
          <details><summary>How are users billed?</summary><p>Plans are subscription-based with monthly or annual billing. Annual subscriptions charge ten months upfront, and growth beyond the plan can be handled through users, storage, support level, and advanced capabilities.</p></details>
          <details><summary>Do you provide training?</summary><p>Yes. Training is part of implementation and can cover executives, finance, procurement, project managers, and site staff.</p></details>
        </div>
      </div>
    </section>

    <section id="demo">
      <div class="wrap">
        <div class="build-demo-grid">
          <div data-reveal>
            <div class="eyebrow">// Demo Requests</div>
            <h2 class="demo-title">See how Navkwa Build would fit your construction company.</h2>
            <p class="demo-copy">Tell us about your company, current tools, and the projects you manage. We&rsquo;ll respond with next steps for a product walkthrough or implementation discussion.</p>
            <div class="contact-info-item">
              <div class="ci-ic"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M4 4h16v16H4z"/><path d="M4 6l8 7 8-7"/></svg></div>
              <div><h5>Email</h5><p>{{ $siteSettings['company_email'] ?? 'owusu-sekyere@navkwa.com' }}</p></div>
            </div>
            <div class="contact-info-item">
              <div class="ci-ic"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.8 19.8 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.8 19.8 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.36 1.9.68 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.9.32 1.85.55 2.81.68A2 2 0 0122 16.92z"/></svg></div>
              <div><h5>Phone &amp; WhatsApp</h5><p>{{ $siteSettings['company_phone'] ?? '+233553544198' }}</p></div>
            </div>
          </div>

          <div class="form-card" data-reveal>
            <form id="navkwaBuildForm" data-contact-form data-status-target="navkwaBuildFormStatus" data-success-target="navkwaBuildSuccess" action="{{ route('contact.store') }}" method="POST">
              @csrf
              <input type="hidden" name="service" value="Navkwa Build demo request">
              <input type="hidden" name="budget" value="Discuss Navkwa Build subscription">
              <input type="hidden" name="timeline" value="Demo requested">
              <label class="field-label">Your name</label>
              <input class="field" name="name" type="text" placeholder="Ama Owusu" required>
              <div class="field-row">
                <div><label class="field-label">Company</label><input class="field" name="company" type="text" placeholder="Construction company" required></div>
                <div><label class="field-label">Country</label><input class="field" name="country" type="text" placeholder="Ghana"></div>
              </div>
              <div class="field-row">
                <div><label class="field-label">Email</label><input class="field" name="email" type="email" placeholder="you@company.com" required></div>
                <div><label class="field-label">Phone</label><input class="field" name="phone" type="tel" placeholder="+233 ..."></div>
              </div>
              <label class="field-label">What should the demo focus on?</label>
              <textarea class="field" name="message" placeholder="Tell us about your active projects, current tools, team size, and the ERP features you want to see."></textarea>
              <button type="submit" class="btn btn-primary btn-sm">Submit Demo Request</button>
            </form>
            <p class="form-status" id="navkwaBuildFormStatus" role="status"></p>
            <div class="success-box" id="navkwaBuildSuccess">
              <div class="success-check"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg></div>
              <h3 class="font-display success-title">Demo request received.</h3>
              <p class="success-copy">Our team will contact you with the next step for Navkwa Build.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer>
    <div class="wrap">
      <div class="footer-grid build-footer-grid">
        <div>
          <a href="{{ route('home') }}" class="logo footer-brand"><span class="dot"></span>Navkwa</a>
          <p class="footer-copy">Building intelligent software that powers African businesses &mdash; from first prototype to enterprise scale.</p>
        </div>
        <div>
          <h6>Products</h6>
          <ul><li><a href="#overview">Navkwa Build</a></li><li><a href="{{ route('home') }}#products">Eduvora</a></li><li><a href="{{ route('home') }}#roadmap">Future Products</a></li></ul>
        </div>
        <div>
          <h6>Solutions</h6>
          <ul><li><a href="#industries">Construction</a></li><li><a href="#industries">Engineering</a></li><li><a href="#industries">Infrastructure</a></li><li><a href="#industries">Developers</a></li></ul>
        </div>
        <div>
          <h6>Resources</h6>
          <ul><li><a href="#docs">Blog</a></li><li><a href="#docs">Documentation</a></li><li><a href="#docs">Case Studies</a></li><li><a href="#faq">Help Centre</a></li><li><a href="{{ route('payments.create') }}">Payments</a></li></ul>
        </div>
        <div>
          <h6>Company</h6>
          <ul><li><a href="{{ route('home') }}">About</a></li><li><a href="{{ route('home') }}#contact">Careers</a></li><li><a href="#">Privacy</a></li><li><a href="#">Terms</a></li><li><a href="#demo">Contact</a></li></ul>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2026 Navkwa Group Ltd. All rights reserved.</p>
        <p class="footer-links-inline"><a href="{{ route('home') }}">Home</a><a href="{{ route('payments.create') }}">Payments</a><a href="#overview">Back to top</a></p>
      </div>
    </div>
  </footer>

  <button id="chat-launcher" aria-label="Open chat">
    <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
  </button>
  <div id="chat-panel">
    <div class="chat-head"><span class="chat-status-dot"></span><div><strong>Navkwa Support</strong><span>Typically replies in a few minutes</span></div></div>
    <div class="chat-body" id="chatBody">
      <div class="chat-bubble">Hi - ask us about Navkwa Build, construction ERP pricing, or booking a demo.</div>
    </div>
    <p class="chat-status-line" id="chatStatus" role="status"></p>
    <form class="chat-foot" id="chatForm">
      <input type="text" id="chatInput" placeholder="Type your message..." aria-label="Chat message" maxlength="2000" required>
      <button type="submit">Send</button>
    </form>
  </div>
</body>
</html>
