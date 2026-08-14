<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title }} - Navkwa Group Ltd.</title>
  <meta name="description" content="{{ $summary }}">
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
</head>
<body class="legal-page">
  <main class="legal-shell">
    <a href="{{ route('home') }}" class="logo"><span class="dot"></span>Navkwa</a>
    <section class="legal-card">
      <p class="eyebrow">// Legal</p>
      <h1>{{ $title }}</h1>
      <p class="legal-summary">{{ $summary }}</p>
      <div class="legal-list">
        @foreach($items as $item)
          <p>{{ $item }}</p>
        @endforeach
      </div>
      <p class="legal-note">These public notices are a launch foundation and can be expanded with formal legal review as Navkwa products and services mature.</p>
      <a href="{{ route('home') }}" class="btn btn-primary btn-sm">&larr; Back to website</a>
    </section>
  </main>
</body>
</html>
