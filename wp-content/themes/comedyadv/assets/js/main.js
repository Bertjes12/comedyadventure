/* === Comedy Adventure — main.js === */
(function () {
  'use strict';

  /* Mobile nav toggle */
  const toggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.nav');
  const body = document.body;

  if (toggle && nav) {
    toggle.addEventListener('click', () => {
      const open = nav.classList.toggle('is-open');
      toggle.classList.toggle('is-open', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      body.classList.toggle('nav-open', open);
    });

    nav.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', () => {
        if (nav.classList.contains('is-open')) {
          nav.classList.remove('is-open');
          toggle.classList.remove('is-open');
          toggle.setAttribute('aria-expanded', 'false');
          body.classList.remove('nav-open');
        }
      });
    });
  }

  /* Header shadow on scroll */
  const header = document.querySelector('.header');
  if (header) {
    const onScroll = () => {
      header.classList.toggle('is-scrolled', window.scrollY > 4);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* Reveal-on-scroll */
  const revealEls = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window && revealEls.length) {
    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            io.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12 }
    );
    revealEls.forEach((el) => io.observe(el));
  } else {
    revealEls.forEach((el) => el.classList.add('is-visible'));
  }

  /* Marquee duplication for seamless loop */
  document.querySelectorAll('.marquee__track').forEach((track) => {
    track.innerHTML += track.innerHTML;
  });

  /* Card carousel: prev/next scroll-by-one-card with snap */
  document.querySelectorAll('[data-card-carousel]').forEach((carousel) => {
    const track = carousel.querySelector('[data-carousel-track]');
    const prev  = carousel.querySelector('[data-carousel-prev]');
    const next  = carousel.querySelector('[data-carousel-next]');
    if (!track || !prev || !next) return;

    const step = () => {
      const first = track.children[0];
      if (!first) return 0;
      const styles = getComputedStyle(track);
      const gap    = parseFloat(styles.columnGap || styles.gap || '0') || 0;
      return first.getBoundingClientRect().width + gap;
    };

    const updateButtons = () => {
      const max = track.scrollWidth - track.clientWidth - 1;
      prev.disabled = track.scrollLeft <= 0;
      next.disabled = track.scrollLeft >= max;
    };

    prev.addEventListener('click', () => track.scrollBy({ left: -step(), behavior: 'smooth' }));
    next.addEventListener('click', () => track.scrollBy({ left:  step(), behavior: 'smooth' }));
    track.addEventListener('scroll', updateButtons, { passive: true });
    window.addEventListener('resize', updateButtons);
    updateButtons();
  });

  /* Locatie foto slider */
  document.querySelectorAll('.locatie-slider').forEach((slider) => {
    const track  = slider.querySelector('[data-slider-track]');
    const prev   = slider.querySelector('[data-slider-prev]');
    const next   = slider.querySelector('[data-slider-next]');
    const dots   = slider.querySelectorAll('[data-slider-dot]');
    const total  = slider.querySelectorAll('.locatie-slider__slide').length;
    if ( !track || total < 2 ) return;

    let current = 0;

    const goTo = (n) => {
      current = (n + total) % total;
      track.style.transform = `translateX(-${current * 100}%)`;
      dots.forEach((d, i) => d.classList.toggle('is-active', i === current));
    };

    if (prev) prev.addEventListener('click', () => goTo(current - 1));
    if (next) next.addEventListener('click', () => goTo(current + 1));
    dots.forEach((d) => d.addEventListener('click', () => goTo(+d.dataset.sliderDot)));

    // Auto-advance elke 5 seconden
    let timer = setInterval(() => goTo(current + 1), 5000);
    slider.addEventListener('mouseenter', () => clearInterval(timer));
    slider.addEventListener('mouseleave', () => { timer = setInterval(() => goTo(current + 1), 5000); });
  });

  /* Hero: parallax orbs + magnetic buttons */
  const heroSection = document.getElementById('hp-hero');
  if (heroSection) {
    const orbs = heroSection.querySelectorAll('.hp-hero__orb, .hp-hero__visual-glow');

    window.addEventListener('scroll', () => {
      const y = window.scrollY;
      orbs.forEach((orb, i) => {
        const dir = i % 2 === 0 ? 1 : -1;
        orb.style.transform = `translate(0, ${y * 0.07 * dir}px)`;
      });
    }, { passive: true });

    heroSection.querySelectorAll('.js-magnetic').forEach((btn) => {
      btn.addEventListener('mousemove', (e) => {
        const r = btn.getBoundingClientRect();
        const x = (e.clientX - r.left - r.width  / 2) * 0.3;
        const y = (e.clientY - r.top  - r.height / 2) * 0.3;
        btn.style.transform = `translate(${x}px, ${y}px)`;
      });
      btn.addEventListener('mouseleave', () => {
        btn.style.transform = '';
      });
    });
  }

  /* Contact form (demo only) */
  const contactForm = document.querySelector('[data-contact-form]');
  if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const status = contactForm.querySelector('[data-form-status]');
      if (status) {
        status.textContent =
          'Bedankt! We nemen binnen 24 uur contact met je op.';
        status.style.color = 'var(--orange)';
      }
      contactForm.reset();
    });
  }
})();
