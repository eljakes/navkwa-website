<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <meta http-equiv="Cache-Control" content="no-store">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>Admin Login - Navkwa Group Ltd.</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
</head>
<body class="admin-auth-page">
  <main class="auth-simple-shell" aria-labelledby="adminLoginTitle">
    <form class="auth-simple-card" method="POST" action="{{ route('admin.login.store') }}" autocomplete="off" data-admin-login-form>
      @csrf

      <a href="{{ route('home') }}" class="auth-brand" aria-label="Go to Navkwa homepage">
        <span class="auth-brand-mark"><span>N</span></span>
        <strong>Navkwa</strong>
      </a>

      <h1 id="adminLoginTitle">Dashboard Login</h1>

      @if($errors->any())
        <div class="auth-alert" role="alert">{{ $errors->first() }}</div>
      @endif

      <div class="auth-field-stack">
        <label class="auth-field">
          <span class="field-label">Email address</span>
          <input class="field" type="email" name="email" value="{{ old('email') }}" placeholder="name@navkwa.com" autocomplete="email" required autofocus>
        </label>

        <label class="auth-field">
          <span class="field-label">Password</span>
          <span class="password-field-wrap">
            <input class="field" id="adminPassword" type="password" name="password" value="" placeholder="Enter password" autocomplete="new-password" autocapitalize="none" spellcheck="false" required data-sensitive-password>
            <button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" data-password-toggle>
              <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke-width="1.8" aria-hidden="true">
                <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
              <svg class="eye-closed" viewBox="0 0 24 24" fill="none" stroke-width="1.8" aria-hidden="true">
                <path d="M3 3l18 18"/>
                <path d="M10.7 5.2A10.4 10.4 0 0 1 12 5c6 0 9.5 7 9.5 7a16.4 16.4 0 0 1-3 3.9"/>
                <path d="M6.1 6.6C3.8 8.2 2.5 12 2.5 12s3.5 7 9.5 7a9.8 9.8 0 0 0 5-1.4"/>
                <path d="M9.9 9.9A3 3 0 0 0 14.1 14"/>
              </svg>
            </button>
          </span>
        </label>
      </div>

      <button class="btn btn-primary btn-lg auth-submit" type="submit">
        Sign In
      </button>

      <a href="{{ route('home') }}" class="auth-back-link">Back to website</a>
    </form>
  </main>
  <script>
    const adminLoginForm = document.querySelector('[data-admin-login-form]');
    const passwordToggle = document.querySelector('[data-password-toggle]');
    const adminPassword = document.getElementById('adminPassword');
    let passwordWasTyped = false;

    const resetPasswordVisibility = () => {
      if (!adminPassword || !passwordToggle) {
        return;
      }

      adminPassword.type = 'password';
      passwordToggle.setAttribute('aria-pressed', 'false');
      passwordToggle.setAttribute('aria-label', 'Show password');
    };

    const clearPassword = (force = false) => {
      if (!adminPassword || (!force && passwordWasTyped)) {
        return;
      }

      adminPassword.value = '';
      adminPassword.defaultValue = '';
      resetPasswordVisibility();
    };

    adminPassword?.addEventListener('keydown', () => {
      passwordWasTyped = true;
    });
    adminPassword?.addEventListener('paste', () => {
      passwordWasTyped = true;
    });
    adminPassword?.addEventListener('input', (event) => {
      if (['insertText', 'insertCompositionText', 'insertFromPaste'].includes(event.inputType)) {
        passwordWasTyped = true;
      }
    });

    const resetPasswordField = () => {
      passwordWasTyped = false;
      clearPassword(true);
    };

    resetPasswordField();
    window.addEventListener('pageshow', resetPasswordField);
    document.addEventListener('visibilitychange', () => {
      if (document.visibilityState === 'visible') {
        resetPasswordField();
      }
    });
    [120, 360, 800].forEach((delay) => {
      window.setTimeout(() => clearPassword(), delay);
    });

    adminLoginForm?.addEventListener('submit', () => {
      resetPasswordVisibility();
    });

    passwordToggle?.addEventListener('click', () => {
      const shouldShow = adminPassword.type === 'password';
      adminPassword.type = shouldShow ? 'text' : 'password';
      passwordToggle.setAttribute('aria-pressed', String(shouldShow));
      passwordToggle.setAttribute('aria-label', shouldShow ? 'Hide password' : 'Show password');
    });
  </script>
</body>
</html>
