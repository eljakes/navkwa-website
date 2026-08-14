<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Make a Payment - Navkwa Group Ltd.</title>
  <meta name="description" content="Pay Navkwa Group Ltd. by Ghana mobile money, Visa, or Mastercard.">
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
</head>
<body class="payment-page">
  <header id="siteHeader" class="scrolled">
    <div class="wrap">
      <nav>
        <a href="{{ route('home') }}" class="logo"><span class="dot"></span>Navkwa</a>
        <div class="nav-cta-group">
          <a href="{{ route('home') }}" class="btn btn-primary btn-sm">Home</a>
        </div>
      </nav>
    </div>
  </header>

  <main class="payment-shell">
    <section class="payment-intro">
      <div>
        <span class="eyebrow">// Secure Payments</span>
        <h1 class="font-display">Pay Navkwa Group Ltd.</h1>
        <p>Clients can pay in Ghana cedis using MTN MoMo, Telecel Cash, AirtelTigo Money, Visa, or Mastercard.</p>
      </div>
    </section>

    <section class="payment-grid" aria-label="Payment details">
      <form class="payment-form" action="{{ route('payments.initialize') }}" method="POST">
        @csrf
        @if($errors->any())
          <div class="payment-alert">
            {{ $errors->first() }}
          </div>
        @endif

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

        <div class="field-row">
          <div>
            <label class="field-label">Amount (GHS)</label>
            <input class="field" name="amount" type="number" min="1" step="0.01" value="{{ old('amount') }}" placeholder="2500.00" required>
          </div>
          <div>
            <label class="field-label">Phone</label>
            <input class="field" name="customer_phone" type="tel" value="{{ old('customer_phone') }}" placeholder="+233 ...">
          </div>
        </div>

        <label class="field-label">Full name</label>
        <input class="field" name="customer_name" type="text" value="{{ old('customer_name') }}" placeholder="Ama Owusu" required>

        <label class="field-label">Email</label>
        <input class="field" name="customer_email" type="email" value="{{ old('customer_email') }}" placeholder="you@company.com" required>

        <label class="field-label">Payment note</label>
        <textarea class="field" name="description" placeholder="Invoice number, project name, or service being paid for">{{ old('description') }}</textarea>

        <button class="btn btn-primary btn-lg" type="submit" data-payment-submit-label>
          {{ old('payment_method') === 'card' ? 'Continue to Card Details' : 'Continue to Payment' }}
        </button>
      </form>
    </section>
  </main>

  <script>
    const paymentMethod = document.querySelector('[data-payment-method]');
    const mobileNetworkField = document.querySelector('[data-mobile-network-field]');
    const cardPaymentNote = document.querySelector('[data-card-payment-note]');
    const submitLabel = document.querySelector('[data-payment-submit-label]');
    function syncPaymentMethod() {
      const needsMobileNetwork = paymentMethod.value === 'mobile_money';
      mobileNetworkField.style.display = needsMobileNetwork ? 'block' : 'none';
      mobileNetworkField.querySelectorAll('input, select, textarea').forEach((field) => {
        field.disabled = !needsMobileNetwork;
      });
      if (cardPaymentNote) {
        cardPaymentNote.hidden = paymentMethod.value !== 'card';
      }
      if (submitLabel) {
        submitLabel.textContent = paymentMethod.value === 'card' ? 'Continue to Card Details' : 'Continue to Payment';
      }
    }
    paymentMethod.addEventListener('change', syncPaymentMethod);
    syncPaymentMethod();
  </script>
</body>
</html>
