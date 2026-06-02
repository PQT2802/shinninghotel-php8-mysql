document.addEventListener('DOMContentLoaded', function () {
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (typeof AOS !== 'undefined') {
    AOS.init({
      duration: 700,
      once: true,
      offset: 40,
      disable: prefersReducedMotion,
    });
  }

  initHeaderNav();
  initRoomSelectForm();
  initLightbox();
  initScrollReveal(prefersReducedMotion);
  initCountUp(prefersReducedMotion);
  initRoomGalleries();
  initSwipers(prefersReducedMotion);
});

function initHeaderNav() {
  const header = document.getElementById('site-header');
  const navToggle = document.getElementById('nav-toggle');
  if (!header || !navToggle) return;

  navToggle.addEventListener('click', function () {
    const open = header.classList.toggle('nav-open');
    navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
  document.querySelectorAll('.main-nav a').forEach(function (link) {
    link.addEventListener('click', function () {
      header.classList.remove('nav-open');
      navToggle.setAttribute('aria-expanded', 'false');
    });
  });
  window.addEventListener(
    'scroll',
    function () {
      header.classList.toggle('is-scrolled', window.scrollY > 8);
    },
    { passive: true }
  );
}

function initRoomSelectForm() {
  const roomForm = document.getElementById('room-select-form');
  if (!roomForm) return;

  const hidden = document.getElementById('selected-room-id');
  const btn = document.getElementById('continue-room-btn');
  roomForm.querySelectorAll('input[name="room_id_radio"]').forEach(function (radio) {
    radio.addEventListener('change', function () {
      if (hidden) hidden.value = radio.value;
      if (btn) btn.disabled = false;
      roomForm.querySelectorAll('.room-select-card').forEach(function (c) {
        c.classList.toggle('selected', c.contains(radio) && radio.checked);
      });
    });
  });
  roomForm.addEventListener('submit', function (e) {
    const selected = roomForm.querySelector('input[name="room_id_radio"]:checked');
    if (!selected) {
      e.preventDefault();
      return;
    }
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'room_id';
    input.value = selected.value;
    roomForm.appendChild(input);
  });
}

function initLightbox() {
  const lightbox = document.getElementById('lightbox');
  if (!lightbox) return;

  const lbImg = lightbox.querySelector('.lightbox-img');
  const closeBtn = lightbox.querySelector('.lightbox-close');

  function openLightbox(src) {
    if (!lbImg || !src) return;
    lbImg.src = src;
    lightbox.hidden = false;
    lightbox.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closeLightbox() {
    lightbox.hidden = true;
    lightbox.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    if (lbImg) lbImg.src = '';
  }

  document.querySelectorAll('.js-lightbox-trigger').forEach(function (img) {
    img.addEventListener('click', function () {
      openLightbox(img.getAttribute('data-full') || img.src);
    });
  });
  if (closeBtn) closeBtn.addEventListener('click', closeLightbox);
  lightbox.addEventListener('click', function (e) {
    if (e.target === lightbox) closeLightbox();
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !lightbox.hidden) closeLightbox();
  });
}

function initScrollReveal(prefersReducedMotion) {
  const reveals = document.querySelectorAll('.reveal');
  if (!reveals.length) return;

  if (prefersReducedMotion) {
    reveals.forEach(function (el) {
      el.classList.add('is-visible');
    });
    return;
  }

  if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
    gsap.registerPlugin(ScrollTrigger);
    reveals.forEach(function (el, i) {
      const stagger = el.closest('.reveal-stagger');
      const delay = stagger ? (i % 6) * 0.08 : 0;
      gsap.fromTo(
        el,
        { opacity: 0, y: 32 },
        {
          opacity: 1,
          y: 0,
          duration: 0.85,
          delay: delay,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: el,
            start: 'top 88%',
            toggleActions: 'play none none none',
          },
        }
      );
    });
    return;
  }

  const observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
  );
  reveals.forEach(function (el) {
    observer.observe(el);
  });
}

function initCountUp(prefersReducedMotion) {
  const counters = document.querySelectorAll('.count-up');
  if (!counters.length) return;

  function animateCounter(el) {
    const target = parseFloat(el.getAttribute('data-target') || '0');
    const suffix = el.getAttribute('data-suffix') || '';
    const decimals = parseInt(el.getAttribute('data-decimals') || '0', 10);
    if (prefersReducedMotion) {
      el.textContent = target.toFixed(decimals) + suffix;
      return;
    }
    const duration = 1800;
    const start = performance.now();
    function tick(now) {
      const progress = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      const value = target * eased;
      el.textContent = value.toFixed(decimals) + suffix;
      if (progress < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  }

  const observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.3 }
  );
  counters.forEach(function (el) {
    observer.observe(el);
  });
}

function initRoomGalleries() {
  document.querySelectorAll('[data-room-gallery]').forEach(function (viewer) {
    const main = viewer.querySelector('#room-gallery-main') || viewer.querySelector('.room-main-image');
    if (!main) return;

    viewer.querySelectorAll('.room-gallery-thumb').forEach(function (btn) {
      btn.addEventListener('click', function () {
        const src = btn.getAttribute('data-src');
        const full = btn.getAttribute('data-full') || src;
        if (!src) return;
        main.src = src;
        main.setAttribute('data-full', full);
        viewer.querySelectorAll('.room-gallery-thumb').forEach(function (t) {
          t.classList.remove('is-active');
          t.setAttribute('aria-selected', 'false');
        });
        btn.classList.add('is-active');
        btn.setAttribute('aria-selected', 'true');
      });
    });
  });
}

function initSwipers(prefersReducedMotion) {
  if (typeof Swiper === 'undefined') return;

  document.querySelectorAll('.testimonials-swiper').forEach(function (el) {
    new Swiper(el, {
      slidesPerView: 1,
      spaceBetween: 24,
      loop: true,
      autoplay: prefersReducedMotion
        ? false
        : { delay: 5000, disableOnInteraction: false },
      pagination: { el: el.querySelector('.swiper-pagination'), clickable: true },
      breakpoints: {
        768: { slidesPerView: 2 },
        992: { slidesPerView: 3 },
      },
    });
  });

}
