@php
  $slides = $carouselSlides ?? collect();
@endphp

<div class="hero-carousel hero-carousel-backdrop" aria-hidden="true">
  @forelse($slides as $slide)
    <div class="hero-slide {{ $loop->first ? 'active' : '' }}" data-slide>
      <img src="{{ $slide['url'] }}" alt="" loading="{{ $loop->first ? 'eager' : 'lazy' }}" decoding="async">
    </div>
  @empty
    <div class="hero-slide hero-slide-fallback fallback-one active" data-slide></div>
    <div class="hero-slide hero-slide-fallback fallback-two" data-slide></div>
    <div class="hero-slide hero-slide-fallback fallback-three" data-slide></div>
  @endforelse
</div>
