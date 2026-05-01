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
      { el: trustItems[0], end: 10,  suffix: '+' },
      { el: trustItems[1], end: 100, suffix: '%' },
      { el: trustItems[2], end: 24,  suffix: '/7' },
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

  /* ── Search toggle ──────────────────────────────────────── */
  var searchToggle = document.getElementById('search-toggle');
  var searchDrawer = document.getElementById('search-drawer');
  var searchInput  = document.getElementById('search-input');

  if (searchToggle && searchDrawer) {
    searchToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      var isOpen = searchDrawer.classList.toggle('open');
      searchToggle.classList.toggle('active', isOpen);
      searchToggle.setAttribute('aria-expanded', isOpen);
      searchDrawer.setAttribute('aria-hidden', !isOpen);
      if (isOpen && searchInput) searchInput.focus();
    });

    document.addEventListener('click', function (e) {
      var searchWrap = document.getElementById('header-search');
      if (searchWrap && !searchWrap.contains(e.target)) {
        searchDrawer.classList.remove('open');
        searchToggle.classList.remove('active');
        searchToggle.setAttribute('aria-expanded', 'false');
        searchDrawer.setAttribute('aria-hidden', 'true');
      }
    });
  }

  /* ── Book Now modal ──────────────────────────────────────── */
  var bookModal    = document.getElementById('book-now-modal');
  var modalClose   = document.getElementById('modal-close');
  var bookingForm  = document.getElementById('booking-form');

  function openModal() {
    if (!bookModal) return;
    bookModal.removeAttribute('hidden');
    document.body.style.overflow = 'hidden';
    var firstInput = bookModal.querySelector('input, select');
    if (firstInput) setTimeout(function () { firstInput.focus(); }, 50);
  }

  function closeModal() {
    if (!bookModal) return;
    bookModal.setAttribute('hidden', '');
    document.body.style.overflow = '';
  }

  // Open on hero "Book Now" button
  var heroBookBtn = document.getElementById('hero-book-btn');
  if (heroBookBtn) {
    heroBookBtn.addEventListener('click', openModal);
  }

  // Also support any element with data-modal="book-now"
  document.querySelectorAll('[data-modal="book-now"]').forEach(function (el) {
    el.addEventListener('click', openModal);
  });

  // Close on × button
  if (modalClose) modalClose.addEventListener('click', closeModal);

  // Close on overlay click (outside dialog)
  if (bookModal) {
    bookModal.addEventListener('click', function (e) {
      if (e.target === bookModal) closeModal();
    });
    // Close on Escape key
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !bookModal.hasAttribute('hidden')) closeModal();
    });
  }

  // Booking form → WhatsApp redirect
  if (bookingForm) {
    bookingForm.addEventListener('submit', function (e) {
      e.preventDefault();

      var name     = (bookingForm.querySelector('#book-name')     || {}).value || '';
      var phone    = (bookingForm.querySelector('#book-phone')    || {}).value || '';
      var email    = (bookingForm.querySelector('#book-email')    || {}).value || '';
      var service  = (bookingForm.querySelector('#book-service')  || {}).value || '';
      var location = (bookingForm.querySelector('#book-location') || {}).value || '';
      var date     = (bookingForm.querySelector('#book-date')     || {}).value || '';
      var message  = (bookingForm.querySelector('#book-message')  || {}).value || '';

      if (!name || !phone || !service || !location) {
        var firstEmpty = bookingForm.querySelector(':invalid');
        if (firstEmpty) firstEmpty.focus();
        return;
      }

      var lines = [
        'Hello Fumitech Phyto Services,',
        '',
        'I would like to book a service:',
        '',
        '• Name: '     + name,
        '• Phone: '    + phone,
      ];
      if (email)    lines.push('• Email: '    + email);
      lines.push(  '• Service: '  + service);
      lines.push(  '• Location: ' + location);
      if (date)     lines.push('• Date: '     + date);
      if (message)  lines.push('• Notes: '    + message);
      lines.push('', 'Please confirm my booking. Thank you!');

      var waMsg = lines.join('\n');
      var waUrl = 'https://wa.me/254734865099?text=' + encodeURIComponent(waMsg);
      window.open(waUrl, '_blank', 'noopener,noreferrer');
      closeModal();
      bookingForm.reset();
    });
  }

  /* ── Enquiry form → WhatsApp ─────────────────────────────── */
  var enquiryForm = document.getElementById('enquiry-form');
  if (enquiryForm) {
    enquiryForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var name    = (enquiryForm.querySelector('#enq-name')    || {}).value || '';
      var phone   = (enquiryForm.querySelector('#enq-phone')   || {}).value || '';
      var email   = (enquiryForm.querySelector('#enq-email')   || {}).value || '';
      var subject = (enquiryForm.querySelector('#enq-subject') || {}).value || '';
      var message = (enquiryForm.querySelector('#enq-message') || {}).value || '';

      if (!name || !phone || !subject || !message) {
        var firstInvalid = enquiryForm.querySelector(':invalid');
        if (firstInvalid) firstInvalid.focus();
        return;
      }

      var lines = [
        'Hello Fumitech Phyto Services,',
        '',
        'I have an enquiry:',
        '',
        '• Name: '    + name,
        '• Phone: '   + phone,
      ];
      if (email)   lines.push('• Email: '   + email);
      lines.push(  '• Subject: ' + subject);
      lines.push(  '• Message: ' + message);
      lines.push('', 'Please get back to me. Thank you!');

      window.open(
        'https://wa.me/254734865099?text=' + encodeURIComponent(lines.join('\n')),
        '_blank', 'noopener,noreferrer'
      );
      enquiryForm.reset();
    });
  }

  /* ── Hero image carousel ────────────────────────────────── */
  var heroSlides   = document.querySelectorAll('.hero-slide');
  var heroDots     = document.querySelectorAll('.hero-dot');
  var heroTitle    = document.querySelector('.hero-title');
  var heroSub      = document.querySelector('.hero-sub');
  var slidesData   = (typeof window.fumiHeroSlides !== 'undefined') ? window.fumiHeroSlides : [];
  var currentSlide = 0;
  var carouselTimer;

  // Store original headline/sub so we can fall back to them
  var defaultTitle = heroTitle ? heroTitle.textContent : '';
  var defaultSub   = heroSub   ? heroSub.textContent   : '';

  function goToSlide(index) {
    if (!heroSlides.length) return;
    var prev = currentSlide;
    currentSlide = (index + heroSlides.length) % heroSlides.length;

    heroSlides[prev].classList.remove('active');
    heroSlides[currentSlide].classList.add('active');

    if (heroDots.length) {
      heroDots[prev].classList.remove('active');
      heroDots[prev].setAttribute('aria-selected', 'false');
      heroDots[currentSlide].classList.add('active');
      heroDots[currentSlide].setAttribute('aria-selected', 'true');
    }

    // Swap headline / sub if the slide has overrides
    var data = slidesData[currentSlide] || {};
    if (heroTitle) heroTitle.textContent = (data.headline && data.headline.trim()) ? data.headline : defaultTitle;
    if (heroSub)   heroSub.textContent   = (data.sub      && data.sub.trim())      ? data.sub      : defaultSub;
  }

  function startCarousel() {
    if (heroSlides.length < 2) return;
    carouselTimer = setInterval(function () {
      goToSlide(currentSlide + 1);
    }, 5000);
  }

  function resetCarousel() {
    clearInterval(carouselTimer);
    startCarousel();
  }

  if (heroSlides.length > 1) {
    // Dot click → jump to slide and reset timer
    heroDots.forEach(function (dot, i) {
      dot.setAttribute('aria-selected', i === 0 ? 'true' : 'false');
      dot.setAttribute('role', 'tab');
      dot.addEventListener('click', function () {
        goToSlide(i);
        resetCarousel();
      });
    });

    startCarousel();
  }

  /* ── Dropdown overflow: flip left when right edge clips ─── */
  document.querySelectorAll('.has-dropdown').forEach(function (item) {
    item.addEventListener('mouseenter', function () {
      var dd = item.querySelector(':scope > .dropdown');
      if (!dd) return;
      var rect = dd.getBoundingClientRect();
      if (rect.right > window.innerWidth - 10) {
        dd.style.left = 'auto';
        dd.style.right = '0';
      }
    });
  });

  /* ── Submenu overflow: flip to left side when right clips ── */
  document.querySelectorAll('.has-submenu').forEach(function (item) {
    item.addEventListener('mouseenter', function () {
      var sm = item.querySelector(':scope > .submenu');
      if (!sm) return;
      var prev = { opacity: sm.style.opacity, visibility: sm.style.visibility };
      sm.style.opacity = '0';
      sm.style.visibility = 'visible';
      var rect = sm.getBoundingClientRect();
      sm.style.opacity = prev.opacity;
      sm.style.visibility = prev.visibility;
      if (rect.right > window.innerWidth - 10) {
        sm.classList.add('submenu--flip-left');
      }
    });
  });

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
