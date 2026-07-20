<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Admin Login - Navkwa Group Ltd.</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
</head>
<body class="admin-page">
  <main class="admin-login-shell">
    <form class="admin-login-card" method="POST" action="{{ route('admin.login.store') }}">
      @csrf
      <a href="{{ route('home') }}" class="logo"><span class="dot"></span>Navkwa</a>
      <span class="eyebrow">// Admin Portal</span>
      <h1 class="font-display">Sign in to operations.</h1>
      <p>Use your individual staff account. Admin pages are marked noindex and protected by Laravel authentication.</p>

      @if($errors->any())
        <div class="payment-alert">{{ $errors->first() }}</div>
      @endif

      <label class="field-label">Email</label>
      <input class="field" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>

      <label class="field-label">Password</label>
      <input class="field" type="password" name="password" autocomplete="current-password" required>

      <label class="admin-check">
        <input type="checkbox" name="remember" value="1">
        <span>Remember this device</span>
      </label>

      <button class="btn btn-primary btn-lg" type="submit">Sign In</button>
    </form>
  </main>
</body>
</html>
