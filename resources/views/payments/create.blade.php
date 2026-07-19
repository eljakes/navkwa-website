<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Make a Payment - Navkwa Group Ltd.</title>
  <meta name="description" content="Pay Navkwa Group Ltd. by Ghana mobile money, Visa, or Mastercard through secure hosted checkout.">
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
          <a href="{{ route('home') }}#contact" class="btn btn-ghost btn-sm">Contact</a>
          <a href="{{ route('home') }}" class="btn btn-primary btn-sm">Website</a>
        </div>
      </nav>
    </div>
  </header>

  <main class="payment-shell">
    <section class="payment-hero">
      <div>
        <span class="eyebrow">// Secure Payments</span>
        <h1 class="font-display">Pay Navkwa Group Ltd.</h1>
        <p>Clients can pay in Ghana cedis using MTN MoMo, Telecel Cash, AirtelTigo Money, Visa, or Mastercard. Card details are handled through secure hosted checkout.</p>
      </div>
    </section>

    <section class="payment-grid">
      <form class="payment-form" action="{{ route('payments.initialize') }}" method="POST">
        @csrf
        @if($errors->any())
          <div class="payment-alert">
            {{ $errors->first() }}
          </div>
        @endif

        <label class="field-label">Payment method</label>
        <select class="field" name="payment_method" id="paymentMethod" required>
          <option value="mobile_money" @selected(old('payment_method', 'mobile_money') === 'mobile_money')>Mobile Money</option>
          <option value="card" @selected(old('payment_method') === 'card')>Visa / Mastercard</option>
        </select>

        <div id="mobileNetworkField">
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

        <button class="btn btn-primary btn-lg" type="submit">Continue to Secure Checkout</button>
      </form>

      <aside class="payment-methods">
        <h2 class="font-display">Available rails</h2>
        <div class="payment-method-card">
          <strong>Mobile Money</strong>
          <span>MTN MoMo, Telecel Cash, AirtelTigo Money</span>
        </div>
        <div class="payment-method-card">
          <strong>Debit Cards</strong>
          <span>Visa and Mastercard through hosted checkout</span>
        </div>
      </aside>
    </section>
  </main>

  <script>
    const paymentMethod = document.getElementById('paymentMethod');
    const mobileNetworkField = document.getElementById('mobileNetworkField');
    function syncPaymentMethod() {
      mobileNetworkField.style.display = paymentMethod.value === 'mobile_money' ? 'block' : 'none';
    }
    paymentMethod.addEventListener('change', syncPaymentMethod);
    syncPaymentMethod();
  </script>
</body>
</html>
