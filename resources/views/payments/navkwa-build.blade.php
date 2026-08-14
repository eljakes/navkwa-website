@php
  $selectedPlanData = $plans[$selectedPlan] ?? reset($plans);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Navkwa Build Payment Details - Navkwa Group Ltd.</title>
  <meta name="description" content="Complete payment details for a Navkwa Build subscription with secure card or mobile money payment.">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
  <script defer src="{{ asset('assets/js/main.js') }}"></script>
</head>
<body class="build-payment-page">
  <header id="siteHeader" class="scrolled">
    <div class="wrap">
      <nav>
        <a href="{{ route('home') }}" class="logo"><span class="dot"></span>Navkwa</a>
        <ul class="nav-links" id="navLinks"></ul>
        <div class="nav-cta-group">
          <a href="{{ route('products.navkwa-build') }}#pricing" class="btn btn-ghost btn-sm">Change Plan</a>
          <button class="menu-toggle" id="menuToggle" aria-label="Menu" aria-controls="navLinks" aria-expanded="false">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
          </button>
        </div>
      </nav>
    </div>
  </header>

  <main class="build-payment-shell">
    <section class="build-payment-intro">
      <div class="wrap">
        <div class="build-payment-grid">
          <div class="build-payment-copy">
            <div class="eyebrow">// Secure Subscription</div>
            <h1 class="font-display">Complete your Navkwa Build payment details.</h1>
            <p>Confirm your plan, choose your billing cycle, enter the billing contact, and continue to payment.</p>
          </div>

          <aside class="payment-summary-card">
            <span class="summary-label">Selected plan</span>
            <h2 class="font-display">{{ $selectedPlanData['name'] }}</h2>
            <p>{{ $currency }} {{ number_format((float) $selectedPlanData['monthly_amount'], 2) }} per month</p>
            <dl>
              <div><dt>Annual billing</dt><dd>{{ $annualBillableMonths }} months billed, two months free</dd></div>
              <div><dt>Projects</dt><dd>Unlimited</dd></div>
              <div><dt>Payment</dt><dd>Mobile money or Visa / Mastercard</dd></div>
            </dl>
          </aside>
        </div>
      </div>
    </section>

    <section class="build-payment-details">
      <div class="wrap">
        <div class="payment-details-layout single">
          <form class="payment-details-card" action="{{ route('payments.initialize') }}" method="POST" data-build-checkout-form data-currency="{{ $currency }}">
            @csrf
            <input type="hidden" name="product" value="navkwa_build">

            @if($errors->any())
              <div class="payment-alert">{{ $errors->first() }}</div>
            @endif

            <div class="payment-form-head">
              <div>
                <h2 class="font-display">Plan and billing</h2>
                <p>Review the selected subscription and billing cycle before payment.</p>
              </div>
            </div>

            <div class="field-row">
              <div>
                <label class="field-label">Subscription plan</label>
                <select class="field" name="plan" data-build-plan-select required>
                  @foreach($plans as $planKey => $plan)
                    @php
                      $monthlyAmount = (float) $plan['monthly_amount'];
                      $annualAmount = $monthlyAmount * (int) $annualBillableMonths;
                    @endphp
                    <option value="{{ $planKey }}" data-monthly="{{ $monthlyAmount }}" data-annual="{{ $annualAmount }}" @selected(old('plan', $selectedPlan) === $planKey)>
                      {{ $plan['name'] }} - {{ $currency }} {{ number_format($monthlyAmount, 2) }}/month
                    </option>
                  @endforeach
                </select>
              </div>
              <div>
                <label class="field-label">Billing cycle</label>
                <select class="field" name="billing_cycle" data-build-billing-select required>
                  <option value="monthly" @selected(old('billing_cycle', $billingCycle) === 'monthly')>Monthly billing</option>
                  <option value="annual" @selected(old('billing_cycle', $billingCycle) === 'annual')>Annual billing - two months free</option>
                </select>
              </div>
            </div>

            <div class="checkout-price-summary" data-build-price-summary>
              Amount will be calculated securely by the backend.
            </div>

            <div class="payment-form-head">
              <div>
                <h2 class="font-display">Payment method</h2>
                <p>Choose mobile money or card payment. If card is selected, the next screen will ask for card number, expiry date, CVV, and any bank authorization step.</p>
              </div>
            </div>

            <label class="field-label">Payment method</label>
            <select class="field" name="payment_method" data-payment-method required>
              <option value="mobile_money" @selected(old('payment_method', 'mobile_money') === 'mobile_money')>Mobile Money</option>
              <option value="card" @selected(old('payment_method') === 'card')>Visa / Mastercard</option>
            </select>

            <div class="card-payment-note" data-card-payment-note @if(old('payment_method', 'mobile_money') !== 'card') hidden @endif>
              <div class="card-fields-preview" aria-hidden="true">
                <div><span>Card number</span><strong>•••• •••• •••• ••••</strong></div>
                <div><span>Expiry</span><strong>MM / YY</strong></div>
                <div><span>CVV</span><strong>•••</strong></div>
              </div>
              <p>Card details are entered on the next secure payment screen after you continue.</p>
            </div>

            <div data-mobile-network-field>
              <label class="field-label">Mobile money network</label>
              <select class="field" name="mobile_network">
                <option value="mtn_momo" @selected(old('mobile_network', 'mtn_momo') === 'mtn_momo')>MTN MoMo</option>
                <option value="telecel_cash" @selected(old('mobile_network') === 'telecel_cash')>Telecel Cash</option>
                <option value="airteltigo_money" @selected(old('mobile_network') === 'airteltigo_money')>AirtelTigo Money</option>
              </select>
            </div>

            <div class="payment-form-head">
              <div>
                <h2 class="font-display">Billing contact</h2>
                <p>Use the company billing contact for payment records and subscription communication.</p>
              </div>
            </div>

            <div class="field-row">
              <div>
                <label class="field-label">Billing name</label>
                <input class="field" name="customer_name" type="text" value="{{ old('customer_name') }}" placeholder="Company or billing contact" required>
              </div>
              <div>
                <label class="field-label">Phone</label>
                <input class="field" name="customer_phone" type="tel" value="{{ old('customer_phone') }}" placeholder="+233 ..." required>
              </div>
            </div>
            <label class="field-label">Email</label>
            <input class="field" name="customer_email" type="email" value="{{ old('customer_email') }}" placeholder="billing@company.com" required>
            <label class="field-label">Payment note</label>
            <textarea class="field" name="description" placeholder="Company name, branch, invoice reference, or subscription note">{{ old('description') }}</textarea>

            <button type="submit" class="btn btn-primary btn-lg" data-payment-submit-label>
              {{ old('payment_method') === 'card' ? 'Continue to Card Details' : 'Continue to Payment' }}
            </button>
          </form>
        </div>
      </div>
    </section>
  </main>
</body>
</html>
