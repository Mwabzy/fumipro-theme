<?php get_header(); ?>

<!-- ══════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════ -->
<section class="hero" id="hero">
    <div class="hero-content">
        <div class="hero-badge">&#x2714; Licensed &amp; Certified Professionals</div>
        <h1 class="hero-title">Protecting Your Home &amp; Business from Pests</h1>
        <p class="hero-sub">Fast, effective, and eco-friendly fumigation services. Available 24/7 for emergency treatments across Nairobi and surrounding areas.</p>
        <div class="hero-btns">
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn-primary" id="hero-quote-btn">Get a Free Quote</a>
            <a href="tel:+254734865099" class="btn-outline" id="hero-call-btn">&#x1F4DE; Call Now</a>
        </div>
        <div class="hero-trust">
            <div class="trust-item"><strong>5,000+</strong><span>Homes Treated</span></div>
            <div class="trust-item"><strong>10+</strong><span>Years Experience</span></div>
            <div class="trust-item"><strong>100%</strong><span>Safe Products</span></div>
            <div class="trust-item"><strong>24/7</strong><span>Emergency Response</span></div>
        </div>
    </div>
    <div class="hero-visual" aria-hidden="true">
        <div class="hero-card-stack">
            <div class="hero-badge-float hero-badge-float--1">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Licensed</span>
            </div>
            <div class="hero-badge-float hero-badge-float--2">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                <span>Eco-Friendly</span>
            </div>
            <div class="hero-badge-float hero-badge-float--3">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/></svg>
                <span>24/7 Response</span>
            </div>
            <div class="hero-circle"></div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════
     EMERGENCY BANNER
══════════════════════════════════════════════════ -->
<div class="emergency-banner" id="emergency-banner">
    <div class="emergency-inner">
        <span class="emerg-pulse"></span>
        <span>&#x1F6A8; 24/7 Emergency Pest Control Available — We respond within 2 hours</span>
        <a href="tel:+254734865099" class="emerg-link" id="emerg-call">Call Now: +254 734 865 099 &rarr;</a>
    </div>
</div>

<!-- ══════════════════════════════════════════════
     SERVICES
══════════════════════════════════════════════════ -->
<section class="section section--white" id="services">
    <div class="section-inner">
        <div class="section-header">
            <span class="section-label">What We Do</span>
            <h2 class="section-title">Complete Pest Control Solutions</h2>
            <p class="section-sub">From residential homes to large commercial facilities, we handle every pest problem with precision and care.</p>
        </div>
        <div class="services-grid">

            <div class="service-card" id="svc-termite">
                <div class="service-icon">&#x1F41C;</div>
                <h3>Termite Control</h3>
                <p>Eliminate termite colonies before they cause serious structural damage to your property.</p>
                <a href="<?php echo esc_url(home_url('/services')); ?>" class="card-link">Learn more &rarr;</a>
            </div>

            <div class="service-card" id="svc-rodent">
                <div class="service-icon">&#x1F400;</div>
                <h3>Rodent Extermination</h3>
                <p>Complete rat and mice removal using safe, humane trapping and exclusion methods.</p>
                <a href="<?php echo esc_url(home_url('/services')); ?>" class="card-link">Learn more &rarr;</a>
            </div>

            <div class="service-card" id="svc-bedbug">
                <div class="service-icon">&#x1F6CF;&#xFE0F;</div>
                <h3>Bed Bug Treatment</h3>
                <p>Heat treatment and chemical solutions that eliminate bed bugs at every life stage.</p>
                <a href="<?php echo esc_url(home_url('/services')); ?>" class="card-link">Learn more &rarr;</a>
            </div>

            <div class="service-card" id="svc-cockroach">
                <div class="service-icon">&#x1F98B;</div>
                <h3>Cockroach Control</h3>
                <p>Targeted gel baits and sprays that wipe out cockroach infestations for good.</p>
                <a href="<?php echo esc_url(home_url('/services')); ?>" class="card-link">Learn more &rarr;</a>
            </div>

            <div class="service-card" id="svc-spider">
                <div class="service-icon">&#x1F577;&#xFE0F;</div>
                <h3>Spider &amp; Insect Control</h3>
                <p>Safe removal of spiders, ants, fleas, and other crawling insects from your space.</p>
                <a href="<?php echo esc_url(home_url('/services')); ?>" class="card-link">Learn more &rarr;</a>
            </div>

            <div class="service-card" id="svc-commercial">
                <div class="service-icon">&#x1F3E2;</div>
                <h3>Commercial Fumigation</h3>
                <p>Full-premises fumigation for warehouses, restaurants, hotels, and offices.</p>
                <a href="<?php echo esc_url(home_url('/services')); ?>" class="card-link">Learn more &rarr;</a>
            </div>

        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════
     WHY CHOOSE US
══════════════════════════════════════════════════ -->
<section class="section section--sky-light" id="why-us">
    <div class="section-inner why-inner">
        <div class="why-visual" aria-hidden="true">
            <div class="why-img-box">
                <div class="why-img-content">
                    <svg width="72" height="72" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="36" cy="36" r="34" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.25)" stroke-width="2"/>
                        <path d="M36 10C27 24 18 26 12 24C12 38 20 52 36 58C52 52 60 38 60 24C54 26 45 24 36 10Z" fill="rgba(255,255,255,0.18)" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
                        <path d="M28 44c-1 2-1 4 0 6M36 45v6M44 44c1 2 1 4 0 6" stroke="white" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="36" cy="33" r="8" fill="rgba(255,255,255,0.2)" stroke="white" stroke-width="1.5"/>
                    </svg>
                    <p class="why-img-label">Trusted Experts</p>
                </div>
                <div class="why-badge-box">
                    <strong>10+</strong>
                    <span>Years Serving Nairobi</span>
                </div>
            </div>
        </div>
        <div class="why-text">
            <span class="section-label">Why Choose Fumitech-Pyto</span>
            <h2 class="section-title why-h2">Trusted by Thousands of Homes &amp; Businesses</h2>
            <p class="section-sub why-sub">We combine industry expertise with cutting-edge techniques to deliver lasting pest control solutions.</p>
            <ul class="why-list">
                <li class="why-item">
                    <div class="why-check"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg></div>
                    <div><strong>Licensed &amp; Certified</strong><p>Certified by national pest control authorities and fully insured.</p></div>
                </li>
                <li class="why-item">
                    <div class="why-check"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg></div>
                    <div><strong>Eco-Friendly Products</strong><p>WHO-approved, family and pet-safe formulations every time.</p></div>
                </li>
                <li class="why-item">
                    <div class="why-check"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg></div>
                    <div><strong>30-Day Guarantee</strong><p>All treatments come with a full satisfaction guarantee.</p></div>
                </li>
                <li class="why-item">
                    <div class="why-check"><svg viewBox="0 0 12 12"><polyline points="2,6 5,9 10,3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg></div>
                    <div><strong>24/7 Emergency Response</strong><p>Severe infestations don't wait — neither do we.</p></div>
                </li>
            </ul>
            <a href="<?php echo esc_url(home_url('/about')); ?>" class="btn-primary" id="why-learn-btn">Learn About Us</a>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════
     PROCESS
══════════════════════════════════════════════════ -->
<section class="section section--blue" id="process">
    <div class="section-inner">
        <div class="section-header">
            <span class="section-label section-label--light">Our Process</span>
            <h2 class="section-title section-title--white">Simple. Effective. Guaranteed.</h2>
            <p class="section-sub section-sub--light">From first call to final follow-up — here's how we work.</p>
        </div>
        <div class="process-grid">
            <div class="process-step" id="step-1">
                <div class="step-num">01</div>
                <h3>Book Inspection</h3>
                <p>Call or fill our form. We schedule a free site inspection at your convenience.</p>
            </div>
            <div class="process-step" id="step-2">
                <div class="step-num">02</div>
                <h3>Treatment Plan</h3>
                <p>Our expert designs a targeted plan specifically for your property and pest type.</p>
            </div>
            <div class="process-step" id="step-3">
                <div class="step-num">03</div>
                <h3>Professional Treatment</h3>
                <p>Our certified team applies the treatment using safe, proven techniques.</p>
            </div>
            <div class="process-step" id="step-4">
                <div class="step-num">04</div>
                <h3>Follow-Up &amp; Guarantee</h3>
                <p>We follow up to ensure the problem is fully resolved under our guarantee.</p>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════
     TESTIMONIALS
══════════════════════════════════════════════════ -->
<section class="section section--white" id="testimonials">
    <div class="section-inner">
        <div class="section-header">
            <span class="section-label">Customer Reviews</span>
            <h2 class="section-title">What Our Clients Say</h2>
        </div>
        <div class="testi-grid">
            <div class="testi-card" id="testi-1">
                <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p>"Fumitech-Pyto treated our office for cockroaches and the results were incredible. Professional team, very thorough. Highly recommend!"</p>
                <strong>Mary N.</strong>
                <span>Westlands, Nairobi</span>
            </div>
            <div class="testi-card" id="testi-2">
                <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p>"We had a severe bed bug problem. Fumitech-Pyto came the next morning and within 48 hours we had zero activity. Amazing service!"</p>
                <strong>James M.</strong>
                <span>Kilimani</span>
            </div>
            <div class="testi-card" id="testi-3">
                <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p>"Reliable, honest, and effective. They set up a monthly prevention plan for us and we haven't had any pest issues since."</p>
                <strong>Sarah K.</strong>
                <span>Industrial Area</span>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════
     CTA BAND
══════════════════════════════════════════════════ -->
<section class="cta-band" id="cta-band">
    <div class="cta-band-inner">
        <h2>Ready for a Pest-Free Property?</h2>
        <p>Get a free inspection and quote today. Our expert team is standing by.</p>
        <div class="cta-btns">
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn-white" id="cta-quote-btn">Get a Free Quote</a>
            <a href="tel:+254734865099" class="btn-outline-white" id="cta-call-btn">&#x1F4DE; +254 734 865 099</a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
