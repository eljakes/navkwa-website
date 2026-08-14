const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const hasGsap = typeof window.gsap !== 'undefined';
const hasScrollTrigger = hasGsap && typeof window.ScrollTrigger !== 'undefined';
const navkwaConfig = window.Navkwa || {};
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

const header = document.getElementById('siteHeader');
const menuToggle = document.getElementById('menuToggle');
const navLinks = document.getElementById('navLinks');

async function fetchJson(url, options = {}) {
  const response = await fetch(url, {
    headers: {
      Accept: 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      ...(options.headers || {})
    },
    ...options
  });

  const data = await response.json().catch(() => ({}));
  if (!response.ok) {
    const message = data.message || 'Something went wrong. Please try again.';
    throw new Error(message);
  }

  return data;
}

function revealImmediately(elements) {
  elements.forEach((el) => {
    el.style.opacity = '1';
    el.style.transform = 'none';
  });
}

function playIntro() {
  if (!document.querySelector('.homepage-hero') || !hasGsap || prefersReducedMotion) return;

  window.gsap.timeline({defaults:{ease:'power2.out'}})
    .from('.hero-content h1', {opacity:0, y:34, duration:0.86})
    .from('.hero-actions > *', {opacity:0, y:18, stagger:0.1, duration:0.62}, '-=0.34');
}

function finishLoader() {
  const loader = document.getElementById('loader');
  if (!loader) {
    playIntro();
    return;
  }

  let didHideLoader = false;
  const hideLoader = () => {
    if (didHideLoader) return;
    didHideLoader = true;
    loader.style.display = 'none';
    playIntro();
  };

  if (hasGsap && !prefersReducedMotion) {
    window.gsap.to('#loader', {
      opacity:0,
      duration:0.6,
      onComplete:hideLoader
    });
    window.setTimeout(hideLoader, 900);
    return;
  }

  hideLoader();
}

window.addEventListener('load', () => {
  const fill = document.getElementById('loaderFill');
  const pct = document.getElementById('loaderPct');
  let progress = 0;

  const timer = setInterval(() => {
    progress += Math.random() * 18;
    if (progress >= 100) {
      progress = 100;
      clearInterval(timer);
    }

    if (fill) fill.style.width = progress + '%';
    if (pct) pct.textContent = String(Math.floor(progress)).padStart(2, '0') + '%';

    if (progress === 100) {
      setTimeout(finishLoader, prefersReducedMotion ? 0 : 250);
    }
  }, prefersReducedMotion ? 20 : 140);
});

window.addEventListener('scroll', () => {
  if (header) header.classList.toggle('scrolled', window.scrollY > 40);
});

if (menuToggle && navLinks) {
  menuToggle.addEventListener('click', () => {
    const isOpen = navLinks.classList.toggle('open');
    menuToggle.setAttribute('aria-expanded', String(isOpen));
  });

  navLinks.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
      navLinks.classList.remove('open');
      menuToggle.setAttribute('aria-expanded', 'false');
    });
  });
}

const heroCarousels = Array.from(document.querySelectorAll('.hero-carousel'));
const heroCarouselState = new WeakMap();

function getHeroSlides(carousel) {
  return carousel ? Array.from(carousel.querySelectorAll('[data-slide]')) : [];
}

function getHeroCarouselRoot(carousel) {
  return carousel.closest('#hero, .about-hero, .brand-page-hero, .page-hero') || document;
}

function getHeroSlideCounter(carousel) {
  const root = getHeroCarouselRoot(carousel);
  return root.querySelector('[data-slide-counter]') || (carousel.id === 'heroCarousel' ? document.getElementById('heroSlideCounter') : null);
}

function getHeroBgDrift(carousel) {
  const root = getHeroCarouselRoot(carousel);
  return root.querySelector('.hero-bg-drift') || (carousel.id === 'heroCarousel' ? document.getElementById('heroBgDrift') : null);
}

function showHeroSlide(carousel, index) {
  const slides = getHeroSlides(carousel);
  if (!slides.length) return;

  const state = heroCarouselState.get(carousel) || { index: 0, timer: null };
  const nextIndex = (index + slides.length) % slides.length;
  const previousIndex = state.index;
  const counter = getHeroSlideCounter(carousel);
  const bgDrift = getHeroBgDrift(carousel);

  if (nextIndex === previousIndex) {
    slides.forEach((slide, slideIndex) => {
      slide.classList.toggle('active', slideIndex === nextIndex);
      slide.classList.remove('exiting');
    });
    if (counter) {
      counter.textContent = `${String(nextIndex + 1).padStart(2, '0')} / ${String(slides.length).padStart(2, '0')}`;
    }
    heroCarouselState.set(carousel, state);
    return;
  }

  const previousSlide = slides[previousIndex];
  const nextSlide = slides[nextIndex];

  slides.forEach((slide, slideIndex) => {
    if (slideIndex !== previousIndex && slideIndex !== nextIndex) {
      slide.classList.remove('active', 'exiting');
    }
  });

  if (previousSlide) {
    const previousImage = previousSlide.querySelector('img');
    if (bgDrift && previousImage) {
      bgDrift.classList.remove('active');
      bgDrift.style.backgroundImage = `url("${previousImage.currentSrc || previousImage.src}")`;
      void bgDrift.offsetWidth;
      bgDrift.classList.add('active');
    }

    previousSlide.classList.remove('active');
    previousSlide.classList.add('exiting');
    window.setTimeout(() => previousSlide.classList.remove('exiting'), 3000);
  }

  if (nextSlide) {
    nextSlide.classList.remove('exiting');
    nextSlide.classList.add('active');
  }

  state.index = nextIndex;
  heroCarouselState.set(carousel, state);

  if (counter) {
    counter.textContent = `${String(state.index + 1).padStart(2, '0')} / ${String(slides.length).padStart(2, '0')}`;
  }
}

function startHeroCarousel(carousel) {
  const slides = getHeroSlides(carousel);
  const state = heroCarouselState.get(carousel) || { index: 0, timer: null };
  showHeroSlide(carousel, state.index);
  if (slides.length <= 1 || prefersReducedMotion) return;

  clearInterval(state.timer);
  state.timer = setInterval(() => {
    const currentState = heroCarouselState.get(carousel) || state;
    showHeroSlide(carousel, currentState.index + 1);
  }, 7800);
  heroCarouselState.set(carousel, state);
}

heroCarousels.forEach(startHeroCarousel);

const revealElements = document.querySelectorAll('[data-reveal]');

if (hasScrollTrigger && !prefersReducedMotion) {
  window.gsap.registerPlugin(window.ScrollTrigger);
  revealElements.forEach((el) => {
    window.gsap.to(el, {
      opacity:1,
      y:0,
      duration:1.15,
      ease:'power3.out',
      scrollTrigger:{ trigger:el, start:'top 85%' }
    });
  });
} else {
  revealImmediately(revealElements);
}

function initCardGlides() {
  if (document.body.classList.contains('admin-page')) return;

  const glideSelectors = [
    '.service-card',
    '.home-robot-media',
    '.home-snapshot-card',
    '.home-product-panel',
    '.home-pathway-grid > *',
    '.industry-tile',
    '.trust-strip > *',
    '.featured-product',
    '.build-erp-showcase',
    '.build-tablet-frame',
    '.active-product-grid > *',
    '.fixam-phone-shell',
    '.roadmap-card',
    '.process-media',
    '.process-grid > *',
    '.why-item',
    '.engagement-grid > *',
    '.trust-grid > *',
    '.work-card',
    '.testi-card',
    '.tech-chip',
    '.cta-panel',
    '.contact-info-item',
    '.form-card',
    '.build-photo-panel',
    '.build-screen',
    '.screen-metrics > div',
    '.screen-table > div',
    '.build-info-grid > *',
    '.industry-check-grid > *',
    '.module-grid > *',
    '.tour-controls > *',
    '.product-shot',
    '.shot-grid > div',
    '.shot-table > div',
    '.mobile-frame > div',
    '.integration-grid > *',
    '.security-grid > *',
    '.timeline-steps > *',
    '.outcome-grid > *',
    '.billing-note',
    '.pricing-card',
    '.plan-summary > p',
    '.plan-feature-groups > div',
    '.pricing-guidance',
    '.feature-comparison',
    '.resource-grid > *',
    '.seo-copy',
    '.faq-grid > *',
    '.payment-summary-card',
    '.payment-details-card',
    '.payment-form',
    '.payment-status-card',
    '.page-industries-grid > *'
  ];

  const cards = Array.from(document.querySelectorAll(glideSelectors.join(',')))
    .filter((card) => !card.closest('#hero') && !card.hasAttribute('data-glide-card'));

  if (!cards.length) return;

  const releaseCard = (card, index = 0) => {
    window.setTimeout(() => {
      card.removeAttribute('data-glide-card');
      card.classList.remove('is-visible');
      card.style.removeProperty('--glide-index');
      card.style.removeProperty('--glide-x');
      card.style.removeProperty('--glide-y');
      card.style.removeProperty('--glide-rx');
      card.style.removeProperty('--glide-rz');
    }, 2100 + (index % 8) * 125);
  };

  cards.forEach((card) => {
    const siblings = Array.from(card.parentElement?.children || []).filter((sibling) => cards.includes(sibling));
    const index = Math.max(0, siblings.indexOf(card));
    const driftOptions = ['-92px', '0px', '92px', '-48px', '48px'];
    const rotateOptions = ['-4deg', '0deg', '4deg', '-2.5deg', '2.5deg'];
    const pitchOptions = ['9deg', '7deg', '9deg', '5deg', '5deg'];

    card.setAttribute('data-glide-card', '');
    card.style.setProperty('--glide-index', String(index % 8));
    card.style.setProperty('--glide-x', driftOptions[index % driftOptions.length]);
    card.style.setProperty('--glide-y', `${96 + (index % 4) * 22}px`);
    card.style.setProperty('--glide-rx', pitchOptions[index % pitchOptions.length]);
    card.style.setProperty('--glide-rz', rotateOptions[index % rotateOptions.length]);
  });

  if (prefersReducedMotion || !('IntersectionObserver' in window)) {
    cards.forEach((card) => {
      card.classList.add('is-visible');
      releaseCard(card);
    });
    return;
  }

  const glideObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      const card = entry.target;
      const index = Number(card.style.getPropertyValue('--glide-index')) || 0;
      card.classList.add('is-visible');
      releaseCard(card, index);
      glideObserver.unobserve(card);
    });
  }, {threshold:0.1, rootMargin:'0px 0px -8% 0px'});

  cards.forEach((card) => glideObserver.observe(card));
}

initCardGlides();

function initTextFlights() {
  if (document.body.classList.contains('admin-page')) return;

  const textSelectors = [
    '.build-hero-copy > .eyebrow',
    '.build-hero-copy > h1',
    '.build-hero-copy > .lead',
    '.build-hero-copy > .hero-actions',
    '.build-product-intro-copy > .eyebrow',
    '.build-product-intro-copy > h1',
    '.build-product-intro-copy > .lead',
    '.build-product-intro-copy > .build-product-intro-actions',
    '.build-product-intro-note > span',
    '.build-product-intro-note > strong',
    '.build-screen-head span',
    '.build-screen-head strong',
    '.dashboard-tabs button',
    '.screen-metrics span',
    '.screen-metrics strong',
    '.timeline-row span',
    '.screen-table span',
    '.screen-table strong',
    '.build-photo-caption span',
    '.build-photo-caption strong',
    '.section-head > .eyebrow',
    '.section-head > h2',
    '.section-head > p',
    '.build-info-grid h3',
    '.build-info-grid p',
    '.industry-check-grid span',
    '.module-grid article > span',
    '.module-grid h3',
    '.module-grid li',
    '.tour-controls button',
    '.shot-header span',
    '.shot-header strong',
    '.shot-grid small',
    '.shot-grid strong',
    '.shot-board span',
    '.shot-table span',
    '.shot-table strong',
    '.mobile-frame small',
    '.mobile-frame strong',
    '.integration-grid span',
    '.security-grid h3',
    '.security-grid p',
    '.timeline-steps span',
    '.timeline-steps strong',
    '.outcome-grid span',
    '.billing-note strong',
    '.billing-note span',
    '.plan-label',
    '.pricing-card h3',
    '.plan-price',
    '.plan-summary p',
    '.plan-feature-groups h4',
    '.plan-feature-groups li',
    '.pricing-guidance h3',
    '.pricing-guidance p',
    '.comparison-head h3',
    '.comparison-head p',
    '.resource-grid article span',
    '.resource-grid h3',
    '.resource-grid p',
    '.resource-grid a',
    '.seo-copy h2',
    '.seo-copy p',
    '.demo-title',
    '.demo-copy',
    '#navkwaBuildForm .field-label',
    '.about-copy > p',
    '.featured-product-copy > .badge-product',
    '.featured-product-copy > h3',
    '.featured-product-copy > p',
    '.featured-product-copy > .featured-product-cta',
    '.featured-product-meta > span',
    '.work-body > .wc-tag',
    '.work-body > h4',
    '.work-body > p',
    '.work-body > .stack',
    '.process-grid article > span',
    '.process-grid article > h3',
    '.process-grid article > p',
    '.why-layout > [data-reveal] > .eyebrow',
    '.why-layout > [data-reveal] > .why-title',
    '.why-layout > [data-reveal] > .why-copy',
    '.why-item h4',
    '.why-item p',
    '.engagement-grid article > h3',
    '.engagement-grid article > p',
    '.trust-grid > span',
    '.home-faq-grid summary',
    '.home-faq-grid p',
    '.contact-info-item h5',
    '.contact-info-item p',
    '.page-hero-copy > .eyebrow',
    '.page-hero-copy > h1',
    '.page-hero-copy > p'
  ];

  const texts = Array.from(document.querySelectorAll(textSelectors.join(',')))
    .filter((text) => !text.closest('#hero') && !text.hasAttribute('data-text-flight'));

  if (!texts.length) return;

  const releaseText = (text, index = 0) => {
    window.setTimeout(() => {
      text.removeAttribute('data-text-flight');
      text.classList.remove('is-visible');
      text.style.removeProperty('--text-flight-index');
      text.style.removeProperty('--text-flight-x');
      text.style.removeProperty('--text-flight-y');
      text.style.removeProperty('--text-flight-rz');
    }, 1800 + (index % 7) * 95);
  };

  texts.forEach((text) => {
    const siblings = Array.from(text.parentElement?.children || []).filter((sibling) => texts.includes(sibling));
    const index = Math.max(0, siblings.indexOf(text));
    const driftOptions = ['-54px', '28px', '-22px', '46px', '0px'];
    const rotateOptions = ['-2.4deg', '1.6deg', '-1.2deg', '2.2deg', '0deg'];

    text.setAttribute('data-text-flight', '');
    text.style.setProperty('--text-flight-index', String(index % 7));
    text.style.setProperty('--text-flight-x', driftOptions[index % driftOptions.length]);
    text.style.setProperty('--text-flight-y', `${58 + (index % 3) * 16}px`);
    text.style.setProperty('--text-flight-rz', rotateOptions[index % rotateOptions.length]);
  });

  if (prefersReducedMotion || !('IntersectionObserver' in window)) {
    texts.forEach((text) => {
      text.classList.add('is-visible');
      releaseText(text);
    });
    return;
  }

  const textObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      const text = entry.target;
      const index = Number(text.style.getPropertyValue('--text-flight-index')) || 0;
      text.classList.add('is-visible');
      releaseText(text, index);
      textObserver.unobserve(text);
    });
  }, { threshold:0.16, rootMargin:'0px 0px -10% 0px' });

  texts.forEach((text) => textObserver.observe(text));
}

initTextFlights();

function animateCounter(el) {
  const target = parseInt(el.dataset.target, 10);
  if (Number.isNaN(target)) return;

  if (hasGsap && !prefersReducedMotion) {
    const value = {val:0};
    window.gsap.to(value, {
      val:target,
      duration:1.4,
      ease:'power1.out',
      onUpdate:() => {
        el.textContent = Math.floor(value.val) + '+';
      }
    });
    return;
  }

  el.textContent = target + '+';
}

document.querySelectorAll('.counter').forEach((el) => {
  if (hasScrollTrigger && !prefersReducedMotion) {
    window.ScrollTrigger.create({
      trigger: el,
      start:'top 90%',
      once:true,
      onEnter: () => animateCounter(el)
    });
    return;
  }

  if ('IntersectionObserver' in window && !prefersReducedMotion) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        animateCounter(el);
        observer.disconnect();
      });
    }, {threshold:0.2});
    observer.observe(el);
    return;
  }

  animateCounter(el);
});

const canvas = document.getElementById('hero-canvas');
const hero = document.getElementById('hero');
const ctx = canvas ? canvas.getContext('2d') : null;
let width = 0;
let height = 0;
let nodes = [];
const mouse = {x:-9999, y:-9999};

function resizeCanvas() {
  if (!canvas || !ctx) return;

  const rect = canvas.getBoundingClientRect();
  const ratio = window.devicePixelRatio || 1;
  width = rect.width || window.innerWidth;
  height = window.innerHeight;

  canvas.width = Math.floor(width * ratio);
  canvas.height = Math.floor(height * ratio);
  ctx.setTransform(ratio, 0, 0, ratio, 0, 0);

  const count = Math.min(70, Math.floor(width / 22));
  nodes = Array.from({length:count}, () => ({
    x: Math.random() * width,
    y: Math.random() * height,
    vx: (Math.random() - 0.5) * 0.35,
    vy: (Math.random() - 0.5) * 0.35,
    r: Math.random() * 1.6 + 0.6
  }));
}

function drawNetwork() {
  if (!ctx) return;

  ctx.clearRect(0, 0, width, height);

  for (const node of nodes) {
    node.x += node.vx;
    node.y += node.vy;
    if (node.x < 0 || node.x > width) node.vx *= -1;
    if (node.y < 0 || node.y > height) node.vy *= -1;
  }

  for (let i = 0; i < nodes.length; i++) {
    const node = nodes[i];
    for (let j = i + 1; j < nodes.length; j++) {
      const other = nodes[j];
      const distance = Math.hypot(node.x - other.x, node.y - other.y);
      if (distance < 140) {
        ctx.strokeStyle = `rgba(96,165,250,${0.09 * (1 - distance / 140)})`;
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(node.x, node.y);
        ctx.lineTo(other.x, other.y);
        ctx.stroke();
      }
    }

    const mouseDistance = Math.hypot(node.x - mouse.x, node.y - mouse.y);
    if (mouseDistance < 160) {
      ctx.strokeStyle = `rgba(16,185,129,${0.18 * (1 - mouseDistance / 160)})`;
      ctx.beginPath();
      ctx.moveTo(node.x, node.y);
      ctx.lineTo(mouse.x, mouse.y);
      ctx.stroke();
    }
  }

  for (const node of nodes) {
    ctx.beginPath();
    ctx.arc(node.x, node.y, node.r, 0, Math.PI * 2);
    ctx.fillStyle = 'rgba(226,232,240,0.65)';
    ctx.fill();
  }

  requestAnimationFrame(drawNetwork);
}

if (canvas && ctx) {
  resizeCanvas();
  window.addEventListener('resize', resizeCanvas);
  drawNetwork();
}

if (hero) {
  hero.addEventListener('mousemove', (event) => {
    mouse.x = event.clientX;
    mouse.y = event.clientY;

    if (!hasGsap || prefersReducedMotion) return;

    const x = (event.clientX / window.innerWidth - 0.5) * 24;
    const y = (event.clientY / window.innerHeight - 0.5) * 24;
    window.gsap.to('.hero-glow.g1', { x:x, y:y, duration:0.8 });
    window.gsap.to('.hero-glow.g2', { x:-x, y:-y, duration:0.8 });
    window.gsap.to('.hero-tech-field', { x:x * 0.42, y:y * 0.28, duration:0.9, ease:'power2.out' });
    window.gsap.to('.ring-one', { x:-x * 0.34, y:y * 0.18, duration:1.1, ease:'power2.out' });
    window.gsap.to('.panel-one', { x:x * 0.26, y:-y * 0.2, duration:1, ease:'power2.out' });
  });

  hero.addEventListener('mouseleave', () => {
    mouse.x = -9999;
    mouse.y = -9999;
    if (!hasGsap || prefersReducedMotion) return;
    window.gsap.to('.hero-tech-field, .ring-one, .panel-one', { x:0, y:0, duration:0.8, ease:'power2.out' });
  });
}

const track = document.getElementById('testiTrack');
let testimonialIndex = 0;

function updateTestimonials() {
  if (!track || !track.children.length) return;

  const cardWidth = track.children[0].getBoundingClientRect().width + 24;
  const maxIndex = window.innerWidth > 900 ? track.children.length - 2 : track.children.length - 1;
  testimonialIndex = Math.max(0, Math.min(testimonialIndex, maxIndex));
  track.style.transform = `translateX(-${testimonialIndex * cardWidth}px)`;
  track.style.transition = 'transform .5s ease';
}

document.getElementById('testiNext')?.addEventListener('click', () => {
  testimonialIndex++;
  updateTestimonials();
});
document.getElementById('testiPrev')?.addEventListener('click', () => {
  testimonialIndex--;
  updateTestimonials();
});
window.addEventListener('resize', updateTestimonials);

document.querySelectorAll('.chip-select').forEach((group) => {
  group.querySelectorAll('.chip-opt').forEach((chip) => {
    chip.addEventListener('click', () => {
      group.querySelectorAll('.chip-opt').forEach((item) => item.classList.remove('sel'));
      chip.classList.add('sel');

      const fieldName = group.dataset.field;
      const ownerForm = group.closest('form');
      const hiddenInput = fieldName
        ? ownerForm?.querySelector(`[data-chip-value="${fieldName}"]`) || document.querySelector(`[data-chip-value="${fieldName}"]`)
        : null;
      if (hiddenInput) hiddenInput.value = chip.textContent.trim();
    });
  });
});

function goStep(stepNumber) {
  document.querySelectorAll('.form-step').forEach((step) => {
    step.classList.toggle('active', step.dataset.step === String(stepNumber));
  });
  document.querySelectorAll('[data-step-dot]').forEach((dot) => {
    dot.classList.toggle('active', parseInt(dot.dataset.stepDot, 10) <= stepNumber);
  });
}

document.querySelectorAll('[data-go-step]').forEach((button) => {
  button.addEventListener('click', () => goStep(parseInt(button.dataset.goStep, 10)));
});

document.querySelectorAll('[data-product-panel-trigger]').forEach((button) => {
  button.addEventListener('click', () => {
    const panelName = button.dataset.productPanelTrigger;
    const tour = button.closest('.tour-layout');
    if (!tour || !panelName) return;

    tour.querySelectorAll('[data-product-panel-trigger]').forEach((item) => {
      item.classList.toggle('active', item === button);
    });
    tour.querySelectorAll('[data-product-panel]').forEach((panelItem) => {
      panelItem.classList.toggle('active', panelItem.dataset.productPanel === panelName);
    });
  });
});

function syncPaymentMethodFields(root = document) {
  root.querySelectorAll('[data-payment-method]').forEach((paymentMethod) => {
    const form = paymentMethod.closest('form') || root;
    const mobileNetworkField = form.querySelector('[data-mobile-network-field]');
    const cardPaymentNote = form.querySelector('[data-card-payment-note]');
    const submitLabel = form.querySelector('[data-payment-submit-label]');

    const sync = () => {
      const needsMobileNetwork = paymentMethod.value === 'mobile_money';
      if (mobileNetworkField) {
        mobileNetworkField.style.display = needsMobileNetwork ? 'block' : 'none';
      }
      mobileNetworkField?.querySelectorAll('input, select, textarea').forEach((field) => {
        field.disabled = !needsMobileNetwork;
      });
      if (cardPaymentNote) {
        cardPaymentNote.hidden = paymentMethod.value !== 'card';
      }
      if (submitLabel) {
        submitLabel.textContent = paymentMethod.value === 'card' ? 'Continue to Card Details' : 'Continue to Payment';
      }
    };

    paymentMethod.addEventListener('change', sync);
    sync();
  });
}

syncPaymentMethodFields();

const buildCheckoutForm = document.querySelector('[data-build-checkout-form]');
const buildPlanSelect = buildCheckoutForm?.querySelector('[data-build-plan-select]');
const buildBillingSelect = buildCheckoutForm?.querySelector('[data-build-billing-select]');
const buildPriceSummary = buildCheckoutForm?.querySelector('[data-build-price-summary]');

function formatBuildAmount(amount, currency) {
  const numericAmount = Number(amount);
  if (!Number.isFinite(numericAmount)) return '';

  try {
    return new Intl.NumberFormat('en-GH', {
      style: 'currency',
      currency: currency || 'GHS'
    }).format(numericAmount);
  } catch (error) {
    return `${currency || 'GHS'} ${numericAmount.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}`;
  }
}

function updateBuildCheckoutSummary() {
  if (!buildPlanSelect || !buildBillingSelect || !buildPriceSummary || !buildCheckoutForm) return;

  const option = buildPlanSelect.selectedOptions[0];
  const billingCycle = buildBillingSelect.value;
  const amount = billingCycle === 'annual' ? option?.dataset.annual : option?.dataset.monthly;
  const cycleText = billingCycle === 'annual' ? 'annual subscription, two months free' : 'monthly subscription';
  const planText = option?.textContent.split(' - ')[0].trim() || 'Selected plan';

  buildPriceSummary.textContent = `${planText}: ${formatBuildAmount(amount, buildCheckoutForm.dataset.currency)} due at checkout for ${cycleText}.`;
}

buildPlanSelect?.addEventListener('change', updateBuildCheckoutSummary);
buildBillingSelect?.addEventListener('change', updateBuildCheckoutSummary);
updateBuildCheckoutSummary();

const contactForms = Array.from(new Set([
  ...document.querySelectorAll('[data-contact-form]'),
  ...document.querySelectorAll('#discoveryForm')
]));

contactForms.forEach((contactForm) => {
  contactForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    const submitButton = form.querySelector('button[type="submit"]');
    const formCard = form.closest('.form-card');
    const status = form.dataset.statusTarget
      ? document.getElementById(form.dataset.statusTarget)
      : document.getElementById('contactFormStatus');
    const successBox = form.dataset.successTarget
      ? document.getElementById(form.dataset.successTarget)
      : formCard?.querySelector('.success-box') || document.getElementById('successBox');
    const stepsRow = form.dataset.stepsTarget
      ? document.querySelector(form.dataset.stepsTarget)
      : formCard?.querySelector('.steps-row');

    if (status) {
      status.className = 'form-status';
      status.textContent = 'Sending your request...';
    }
    if (submitButton) submitButton.disabled = true;

    try {
      await fetchJson(form.action || navkwaConfig.routes?.contact, {
        method: 'POST',
        body: new FormData(form)
      });

      form.style.display = 'none';
      if (stepsRow) stepsRow.style.display = 'none';
      successBox?.classList.add('show');
      if (status) {
        status.className = 'form-status ok';
        status.textContent = 'Saved to the backend inbox.';
      }
    } catch (error) {
      if (status) {
        status.className = 'form-status error';
        status.textContent = error.message;
      }
    } finally {
      if (submitButton) submitButton.disabled = false;
    }
  });
});

const launcher = document.getElementById('chat-launcher');
const panel = document.getElementById('chat-panel');
const chatForm = document.getElementById('chatForm');
const chatInput = document.getElementById('chatInput');
const chatBody = document.getElementById('chatBody');
const chatStatus = document.getElementById('chatStatus');
const chatSessionStorageKey = 'navkwa_chat_session_id';
let lastChatMessageId = 0;
let chatPollTimer = null;

launcher?.addEventListener('click', () => {
  panel?.classList.toggle('open');
  loadChatHistory({afterLastSeen: true});
});

function setChatStatus(message = '', tone = '') {
  if (!chatStatus) return;
  chatStatus.textContent = message;
  chatStatus.className = `chat-status-line${tone ? ` ${tone}` : ''}`;
}

function rememberLastChatMessage(message) {
  if (message?.id) {
    lastChatMessageId = Math.max(lastChatMessageId, Number(message.id) || 0);
  }
}

function appendChatBubble(message, sender = 'support', id = null) {
  if (!chatBody) return;

  if (id && chatBody.querySelector(`[data-chat-message-id="${id}"]`)) return;

  const bubble = document.createElement('div');
  bubble.className = `chat-bubble${sender === 'user' ? ' user' : ''}`;
  if (id) bubble.dataset.chatMessageId = String(id);
  bubble.textContent = message;
  chatBody.appendChild(bubble);
  chatBody.scrollTop = chatBody.scrollHeight;
}

async function loadChatHistory(options = {}) {
  const sessionId = localStorage.getItem(chatSessionStorageKey);
  if (!sessionId || !navkwaConfig.routes?.chatHistory || !chatBody) return;

  try {
    const params = new URLSearchParams({session_id: sessionId});
    if (options.afterLastSeen && lastChatMessageId > 0) {
      params.set('after_id', String(lastChatMessageId));
    }

    const data = await fetchJson(`${navkwaConfig.routes.chatHistory}?${params.toString()}`);
    if (!data.messages?.length) return;

    if (!options.afterLastSeen) chatBody.innerHTML = '';
    data.messages.forEach((message) => {
      appendChatBubble(message.message, message.sender, message.id);
      rememberLastChatMessage(message);
    });
  } catch (error) {
    console.warn('Unable to load chat history', error);
  }
}

loadChatHistory();

function startChatPolling() {
  if (chatPollTimer || !navkwaConfig.routes?.chatHistory) return;

  chatPollTimer = window.setInterval(() => {
    if (!localStorage.getItem(chatSessionStorageKey)) return;
    loadChatHistory({afterLastSeen: true});
  }, 7000);
}

startChatPolling();

chatForm?.addEventListener('submit', async (event) => {
  event.preventDefault();
  const text = chatInput?.value.trim();
  if (!text || !chatBody || !chatInput || !navkwaConfig.routes?.chatMessages) return;

  chatInput.value = '';
  chatInput.disabled = true;
  setChatStatus('Sending to Navkwa support...');

  try {
    const data = await fetchJson(navkwaConfig.routes.chatMessages, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        session_id: localStorage.getItem(chatSessionStorageKey),
        message: text,
        source_url: window.location.href,
        source_title: document.title
      })
    });

    if (data.session_id) localStorage.setItem(chatSessionStorageKey, data.session_id);
    (data.messages || []).forEach((message) => {
      appendChatBubble(message.message, message.sender, message.id);
      rememberLastChatMessage(message);
    });
    setChatStatus('Message delivered. A Navkwa team member can reply here from the dashboard.', 'ok');
    startChatPolling();
  } catch (error) {
    chatInput.value = text;
    setChatStatus('The message could not be sent. Please try again or use the contact form.', 'error');
  } finally {
    chatInput.disabled = false;
    chatInput.focus();
  }
});

if (!document.body.classList.contains('admin-page') && !prefersReducedMotion && window.matchMedia('(pointer:fine)').matches) {
  const tiltSelectors = [
    '.service-card',
    '.home-snapshot-card',
    '.home-pathway-grid > a',
    '.home-product-panel',
    '.industry-tile',
    '.featured-product',
    '.build-erp-showcase',
    '.active-product-grid > *',
    '.roadmap-card',
    '.work-card',
    '.engagement-grid article',
    '.trust-grid span',
    '.industry-photo-card',
    '.pricing-card',
    '.plan-feature-groups > div',
    '.pricing-guidance',
    '.feature-comparison',
    '.faq-grid details',
    '.integration-grid span',
    '.security-grid article',
    '.timeline-steps > div',
    '.outcome-grid span',
    '.resource-grid article',
    '.build-info-grid div',
    '.module-grid article',
    '.product-shot',
    '.build-screen',
    '.build-photo-panel',
    '.payment-summary-card',
    '.payment-details-card',
    '.payment-form'
  ];

  document.querySelectorAll(tiltSelectors.join(',')).forEach((card) => {
    card.setAttribute('data-tilt-card', '');

    card.addEventListener('pointermove', (event) => {
      const rect = card.getBoundingClientRect();
      const x = (event.clientX - rect.left) / rect.width - 0.5;
      const y = (event.clientY - rect.top) / rect.height - 0.5;
      card.style.setProperty('--card-glow-x', `${((x + 0.5) * 100).toFixed(1)}%`);
      card.style.setProperty('--card-glow-y', `${((y + 0.5) * 100).toFixed(1)}%`);
      card.style.transform = `perspective(1100px) rotateX(${(-y * 2.6).toFixed(2)}deg) rotateY(${(x * 3.4).toFixed(2)}deg) translateY(-14px) scale(1.025)`;
    });

    card.addEventListener('pointerleave', () => {
      card.style.transform = '';
      card.style.removeProperty('--card-glow-x');
      card.style.removeProperty('--card-glow-y');
    });
  });
}
