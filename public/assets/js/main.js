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
  if (!hasGsap || prefersReducedMotion) return;

  window.gsap.timeline()
    .from('.hero-content .eyebrow', {opacity:0, y:16, duration:0.6})
    .from('.hero-content h1', {opacity:0, y:26, duration:0.8}, '-=0.35')
    .from('.hero-content .lead', {opacity:0, y:20, duration:0.7}, '-=0.5')
    .from('.hero-actions > *', {opacity:0, y:16, stagger:0.1, duration:0.6}, '-=0.4')
    .from('.hero-strip', {opacity:0, y:16, duration:0.6}, '-=0.3')
    .from('.hero-visual', {opacity:0, x:28, duration:0.9}, '-=0.8');
}

function finishLoader() {
  const loader = document.getElementById('loader');
  if (!loader) {
    playIntro();
    return;
  }

  if (hasGsap && !prefersReducedMotion) {
    window.gsap.to('#loader', {
      opacity:0,
      duration:0.6,
      onComplete:() => {
        loader.style.display = 'none';
        playIntro();
      }
    });
    return;
  }

  loader.style.display = 'none';
  playIntro();
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

const heroCarousel = document.getElementById('heroCarousel');
const heroSlideCounter = document.getElementById('heroSlideCounter');
const heroBgDrift = document.getElementById('heroBgDrift');
let heroSlideIndex = 0;
let heroSlideTimer = null;

function getHeroSlides() {
  return heroCarousel ? Array.from(heroCarousel.querySelectorAll('[data-slide]')) : [];
}

function showHeroSlide(index) {
  const slides = getHeroSlides();
  if (!slides.length) return;

  const nextIndex = (index + slides.length) % slides.length;
  const previousIndex = heroSlideIndex;

  if (nextIndex === previousIndex) {
    slides.forEach((slide, slideIndex) => {
      slide.classList.toggle('active', slideIndex === nextIndex);
      slide.classList.remove('exiting');
    });
    if (heroSlideCounter) {
      heroSlideCounter.textContent = `${String(nextIndex + 1).padStart(2, '0')} / ${String(slides.length).padStart(2, '0')}`;
    }
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
    if (heroBgDrift && previousImage) {
      heroBgDrift.classList.remove('active');
      heroBgDrift.style.backgroundImage = `url("${previousImage.currentSrc || previousImage.src}")`;
      void heroBgDrift.offsetWidth;
      heroBgDrift.classList.add('active');
    }

    previousSlide.classList.remove('active');
    previousSlide.classList.add('exiting');
    window.setTimeout(() => previousSlide.classList.remove('exiting'), 3000);
  }

  if (nextSlide) {
    nextSlide.classList.remove('exiting');
    nextSlide.classList.add('active');
  }

  heroSlideIndex = nextIndex;

  if (heroSlideCounter) {
    heroSlideCounter.textContent = `${String(heroSlideIndex + 1).padStart(2, '0')} / ${String(slides.length).padStart(2, '0')}`;
  }
}

function startHeroCarousel() {
  const slides = getHeroSlides();
  showHeroSlide(heroSlideIndex);
  if (slides.length <= 1 || prefersReducedMotion) return;

  clearInterval(heroSlideTimer);
  heroSlideTimer = setInterval(() => showHeroSlide(heroSlideIndex + 1), 7800);
}

startHeroCarousel();

const revealElements = document.querySelectorAll('[data-reveal]');

if (hasScrollTrigger && !prefersReducedMotion) {
  window.gsap.registerPlugin(window.ScrollTrigger);
  revealElements.forEach((el) => {
    window.gsap.to(el, {
      opacity:1,
      y:0,
      duration:0.9,
      ease:'power2.out',
      scrollTrigger:{ trigger:el, start:'top 85%' }
    });
  });
} else {
  revealImmediately(revealElements);
}

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
        ctx.strokeStyle = `rgba(6,182,212,${0.14 * (1 - distance / 140)})`;
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(node.x, node.y);
        ctx.lineTo(other.x, other.y);
        ctx.stroke();
      }
    }

    const mouseDistance = Math.hypot(node.x - mouse.x, node.y - mouse.y);
    if (mouseDistance < 160) {
      ctx.strokeStyle = `rgba(139,92,246,${0.35 * (1 - mouseDistance / 160)})`;
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
  });

  hero.addEventListener('mouseleave', () => {
    mouse.x = -9999;
    mouse.y = -9999;
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
      const hiddenInput = fieldName ? document.querySelector(`[data-chip-value="${fieldName}"]`) : null;
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

const discoveryForm = document.getElementById('discoveryForm');
const contactFormStatus = document.getElementById('contactFormStatus');

discoveryForm?.addEventListener('submit', async (event) => {
  event.preventDefault();
  const form = event.currentTarget;
  const submitButton = form.querySelector('button[type="submit"]');

  if (contactFormStatus) {
    contactFormStatus.className = 'form-status';
    contactFormStatus.textContent = 'Sending your request...';
  }
  if (submitButton) submitButton.disabled = true;

  try {
    await fetchJson(form.action || navkwaConfig.routes?.contact, {
      method: 'POST',
      body: new FormData(form)
    });

    form.style.display = 'none';
    document.querySelector('.steps-row').style.display = 'none';
    document.getElementById('successBox')?.classList.add('show');
    if (contactFormStatus) {
      contactFormStatus.className = 'form-status ok';
      contactFormStatus.textContent = 'Saved to the backend inbox.';
    }
  } catch (error) {
    if (contactFormStatus) {
      contactFormStatus.className = 'form-status error';
      contactFormStatus.textContent = error.message;
    }
  } finally {
    if (submitButton) submitButton.disabled = false;
  }
});

const launcher = document.getElementById('chat-launcher');
const panel = document.getElementById('chat-panel');
const chatForm = document.getElementById('chatForm');
const chatInput = document.getElementById('chatInput');
const chatBody = document.getElementById('chatBody');
const chatSessionStorageKey = 'navkwa_chat_session_id';

launcher?.addEventListener('click', () => panel?.classList.toggle('open'));

function appendChatBubble(message, sender = 'support') {
  if (!chatBody) return;

  const bubble = document.createElement('div');
  bubble.className = `chat-bubble${sender === 'user' ? ' user' : ''}`;
  bubble.textContent = message;
  chatBody.appendChild(bubble);
  chatBody.scrollTop = chatBody.scrollHeight;
}

async function loadChatHistory() {
  const sessionId = localStorage.getItem(chatSessionStorageKey);
  if (!sessionId || !navkwaConfig.routes?.chatHistory || !chatBody) return;

  try {
    const params = new URLSearchParams({session_id: sessionId});
    const data = await fetchJson(`${navkwaConfig.routes.chatHistory}?${params.toString()}`);
    if (!data.messages?.length) return;

    chatBody.innerHTML = '';
    data.messages.forEach((message) => appendChatBubble(message.message, message.sender));
  } catch (error) {
    console.warn('Unable to load chat history', error);
  }
}

loadChatHistory();

chatForm?.addEventListener('submit', async (event) => {
  event.preventDefault();
  const text = chatInput?.value.trim();
  if (!text || !chatBody || !chatInput || !navkwaConfig.routes?.chatMessages) return;

  chatInput.value = '';
  appendChatBubble(text, 'user');

  try {
    const data = await fetchJson(navkwaConfig.routes.chatMessages, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        session_id: localStorage.getItem(chatSessionStorageKey),
        message: text
      })
    });

    if (data.session_id) localStorage.setItem(chatSessionStorageKey, data.session_id);
    const reply = (data.messages || []).find((message) => message.sender !== 'user');
    if (reply) appendChatBubble(reply.message, reply.sender);
  } catch (error) {
    appendChatBubble('The message could not be sent. Please try again or use the contact form.', 'support');
  }
});
