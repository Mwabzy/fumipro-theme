/**
 * FumiPro Theme — main.js
 */

(function () {
  'use strict';

  /* ── Sticky header shadow on scroll ─────────────────────── */
  const header = document.getElementById('site-header');
  if (header) {
    window.addEventListener('scroll', function () {
      header.classList.toggle('scrolled', window.scrollY > 40);
    }, { passive: true });
  }

  /* ── Mobile hamburger toggle ─────────────────────────────── */
  const hamburger = document.getElementById('hamburger');
  const mobileNav = document.getElementById('mobile-nav');
  if (hamburger && mobileNav) {
    hamburger.addEventListener('click', function () {
      const isOpen = hamburger.classList.toggle('open');
      mobileNav.classList.toggle('open', isOpen);
      hamburger.setAttribute('aria-expanded', isOpen);
      mobileNav.setAttribute('aria-hidden', !isOpen);
    });

    // Close on outside click
    document.addEventListener('click', function (e) {
      if (!header.contains(e.target)) {
        hamburger.classList.remove('open');
        mobileNav.classList.remove('open');
        hamburger.setAttribute('aria-expanded', 'false');
        mobileNav.setAttribute('aria-hidden', 'true');
      }
    });
  }

  /* ── Mobile accordion toggles ───────────────────────────── */
  document.querySelectorAll('.mob-toggle').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      var dropdown = btn.closest('.mob-header').nextElementSibling;
      if (!dropdown) return;
      var isOpen = dropdown.classList.toggle('open');
      btn.classList.toggle('open', isOpen);
      btn.setAttribute('aria-expanded', isOpen);
      btn.textContent = isOpen ? '×' : '+';
    });
  });

  /* ── Smooth scroll for anchor links ─────────────────────── */
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        e.preventDefault();
        const headerOffset = parseInt(
          getComputedStyle(document.documentElement)
            .getPropertyValue('--header-total') || '102'
        );
        const top = target.getBoundingClientRect().top + window.scrollY - headerOffset;
        window.scrollTo({ top: top, behavior: 'smooth' });

        // Close mobile nav if open
        if (hamburger && mobileNav) {
          hamburger.classList.remove('open');
          mobileNav.classList.remove('open');
          hamburger.setAttribute('aria-expanded', 'false');
          mobileNav.setAttribute('aria-hidden', 'true');
        }
      }
    });
  });

  /* ── Scroll-reveal animation (Intersection Observer) ─────── */
  const revealItems = document.querySelectorAll(
    '.service-card, .process-step, .testi-card, .why-item, .trust-item'
  );

  if ('IntersectionObserver' in window && revealItems.length) {
    // Set initial state via JS (avoids CSS flash before observer runs)
    revealItems.forEach(function (el, i) {
      el.style.opacity = '0';
      el.style.transform = 'translateY(20px)';
      el.style.transition = 'opacity 0.5s ease ' + (i % 4) * 0.08 + 's, transform 0.5s ease ' + (i % 4) * 0.08 + 's';
    });

    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    revealItems.forEach(function (el) {
      observer.observe(el);
    });
  }

  /* ── Counter animation for trust stats ──────────────────── */
  function animateCounter(el, end, suffix) {
    var start = 0;
    var duration = 1800;
    var startTime = null;

    function step(timestamp) {
      if (!startTime) startTime = timestamp;
      var progress = Math.min((timestamp - startTime) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.floor(eased * end).toLocaleString() + suffix;
      if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  var trustItems = document.querySelectorAll('.trust-item strong');
  if (trustItems.length && 'IntersectionObserver' in window) {
    var counters = [
      { el: trustItems[0], end: 5000, suffix: '+' },
      { el: trustItems[1], end: 10,   suffix: '+' },
      { el: trustItems[2], end: 100,  suffix: '%' },
      { el: trustItems[3], end: 24,   suffix: '/7' },
    ];

    var counterObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          var matched = counters.find(function (c) { return c.el === entry.target; });
          if (matched) animateCounter(matched.el, matched.end, matched.suffix);
          counterObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });

    counters.forEach(function (c) {
      if (c.el) counterObserver.observe(c.el);
    });
  }

  /* ── Active nav link on scroll ──────────────────────────── */
  var sections = document.querySelectorAll('section[id], div[id="emergency-banner"]');
  var navLinks = document.querySelectorAll('.desktop-nav a, .mobile-nav a');

  if (sections.length && navLinks.length && 'IntersectionObserver' in window) {
    var sectionObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          navLinks.forEach(function (link) {
            var href = link.getAttribute('href');
            if (href && href.includes('#' + entry.target.id)) {
              link.classList.add('active');
            } else {
              link.classList.remove('active');
            }
          });
        }
      });
    }, { threshold: 0.4 });

    sections.forEach(function (s) { sectionObserver.observe(s); });
  }

})();
