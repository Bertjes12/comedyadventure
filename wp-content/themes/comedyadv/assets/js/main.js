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
