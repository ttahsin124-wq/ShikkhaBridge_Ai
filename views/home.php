<?php
$signedIn = Auth::check();
$startUrl = $signedIn ? url(portal_home(Auth::user())) : url('/signup?as=student');
?>
<!-- ============ Hero ============ -->
<section class="hero" aria-labelledby="hero-title">
  <div class="container">
    <div class="row align-items-center g-4 g-lg-5">
      <div class="col-lg-6">
        <span class="eyebrow hero-in"><span class="eyebrow-dot"></span> AI tutor in Bangla &amp; English</span>
        <h1 class="hero-title hero-in" id="hero-title" style="--delay:.08s">
          Your Learning Journey, <span class="text-grad">Reimagined</span> with AI.
        </h1>
        <p class="hero-sub hero-in" style="--delay:.16s">
          ShikkhaBridge AI learns how you learn. It explains concepts in simple language, finds the topics you find hard,
          and builds practice around them, while your teachers see exactly where to help.
        </p>
        <div class="d-flex flex-wrap gap-3 mt-4 hero-in" style="--delay:.24s">
          <a class="btn btn-primary btn-lg" href="<?= $startUrl ?>"><?= $signedIn ? 'Go to my portal' : 'Start Learning' ?> <?= icon('arrow-right') ?></a>
          <a class="btn btn-outline-secondary btn-lg" href="#features"><?= icon('play') ?> Explore Features</a>
        </div>
        <div class="hero-trust hero-in" style="--delay:.32s">
          <span><?= icon('check') ?> Free for students</span>
          <span><?= icon('check') ?> Teacher-reviewed content</span>
          <span><?= icon('check') ?> Private &amp; secure</span>
        </div>
      </div>

      <div class="col-lg-6">
        <!-- 3D illustration: an AI core linked to books, a graduation cap and live learning cards -->
        <div class="scene" data-parallax aria-hidden="true">
          <div class="scene-glow"></div>
          <div class="scene-stage">
            <svg class="scene-links" viewBox="0 0 100 100" preserveAspectRatio="none">
              <defs>
                <linearGradient id="linkGrad" x1="0" y1="0" x2="1" y2="1">
                  <stop offset="0" stop-color="#8fb0e6" stop-opacity=".15"/>
                  <stop offset=".5" stop-color="#8fb0e6" stop-opacity=".9"/>
                  <stop offset="1" stop-color="#7fb3e8" stop-opacity=".3"/>
                </linearGradient>
              </defs>
              <path d="M22 16 C 32 26, 40 40, 50 50"/>
              <path d="M84 40 C 72 42, 62 46, 50 50"/>
              <path d="M20 76 C 30 68, 40 58, 50 50"/>
              <path d="M78 90 C 70 78, 60 62, 50 50"/>
              <path d="M80 10 C 72 24, 60 38, 50 50"/>
              <path d="M44 92 C 46 78, 48 64, 50 50"/>
            </svg>

            <div class="rings"><span class="ring r1"></span><span class="ring r2"></span><span class="ring r3"></span></div>
            <div class="core"><div class="core-orb"><?= icon('sparkles') ?></div><span class="core-label">SHIKKHA AI</span></div>

            <div class="obj glyph glyph-1 glass bob" style="--d:-1s">&Sigma;</div>
            <div class="obj glyph glyph-2 glass bob" style="--d:-3s">অ</div>
            <div class="obj glyph glyph-3 glass bob" style="--d:-5s">&pi;</div>

            <div class="obj obj-book"><div class="bob" style="--d:-2s">
              <div class="book3d"><span class="b-back"></span><span class="b-spine"></span><span class="b-pages"></span><span class="b-top"></span><span class="b-front"><?= icon('cap') ?></span></div>
            </div></div>

            <div class="obj obj-cap"><div class="bob" style="--d:-4s">
              <svg viewBox="0 0 120 100">
                <defs>
                  <linearGradient id="capTop" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#8fb0e6"/><stop offset="1" stop-color="#26478f"/></linearGradient>
                  <linearGradient id="capBase" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#14215a"/><stop offset="1" stop-color="#0b1640"/></linearGradient>
                </defs>
                <path d="M28 46v18c0 11 64 11 64 0V46L60 59z" fill="url(#capBase)"/>
                <polygon points="5,32 60,54 60,61 5,39" fill="#1c3278"/>
                <polygon points="60,54 115,32 115,39 60,61" fill="#121d50"/>
                <polygon points="60,10 115,32 60,54 5,32" fill="url(#capTop)"/>
                <polygon points="60,14 104,32 60,50 16,32" fill="#fff" opacity=".08"/>
                <path d="M60 32 L98 42 L98 68" stroke="#7fb3e8" stroke-width="2.6" fill="none" stroke-linecap="round"/>
                <circle cx="98" cy="72" r="5.5" fill="#7fb3e8"/>
                <circle cx="60" cy="32" r="3.6" fill="#d6e2fa"/>
              </svg>
            </div></div>

            <div class="obj card-tutor"><div class="float-card glass bob">
              <div class="fc-head"><span class="icon-3d sm"><?= icon('sparkles') ?></span><span>AI Tutor<div class="fc-sub">Explaining in Bangla</div></span><span class="online-dot"></span></div>
              <div class="bubble bubble-ai py-2 px-2">বল = ভর × ত্বরণ. Push harder, and it speeds up faster.</div>
            </div></div>

            <div class="obj card-mastery"><div class="float-card glass bob" style="--d:-2.5s">
              <div class="fc-sub mb-1">Algebra mastery</div>
              <div class="d-flex align-items-end justify-content-between mb-2"><span class="fc-big">82%</span><span class="fc-up">+12% this week</span></div>
              <div class="progress" style="--bs-progress-height:.45rem"><div class="progress-bar" style="width:82%"></div></div>
            </div></div>

            <div class="obj card-weak"><div class="float-card glass bob" style="--d:-4.5s">
              <div class="fc-head mb-1"><span class="icon-3d sm tone-cyan"><?= icon('target') ?></span><span>Weak topic found<div class="fc-sub">Motion · 45% accuracy</div></span></div>
              <div class="d-flex justify-content-end"><span class="chip-weak">Practise now &rarr;</span></div>
            </div></div>

            <div class="obj card-group"><div class="float-card glass bob" style="--d:-1.5s">
              <div class="avatars mb-2"><span>NJ</span><span>RA</span><span>MK</span><span>+3</span></div>
              <div class="fw-bold">Physics study group</div>
              <div class="fc-sub">6 members studying now</div>
            </div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ Features ============ -->
<section class="section" id="features" aria-labelledby="features-title">
  <div class="container">
    <div class="section-head" data-reveal>
      <span class="eyebrow"><?= icon('zap') ?> Features</span>
      <h2 id="features-title">Everything a learner needs, <span class="text-grad">working together</span></h2>
      <p>An AI tutor, adaptive practice and real teachers, connected so every study session builds on the last one.</p>
    </div>

    <div class="row g-4">
      <div class="col-lg-7" data-reveal>
        <article class="glass feature-card feature-lg" data-tilt>
          <div class="d-flex align-items-center gap-3">
            <span class="icon-3d"><?= icon('sparkles') ?></span>
            <span class="feature-tag">Gemini-powered</span>
          </div>
          <h3>A personal AI tutor that speaks your language</h3>
          <p>Ask anything and get a clear, step-by-step explanation in simple Bangla or English. Turn any topic into practice questions, or summarise a long resource in seconds.</p>
          <div class="mock" aria-hidden="true">
            <div class="bubble bubble-me">Explain Newton's second law simply</div>
            <div class="bubble bubble-ai"><strong>F = m × a.</strong> The harder you push something, the faster it speeds up, and heavier things need a bigger push to speed up the same amount.</div>
            <div class="typing"><i></i><i></i><i></i></div>
          </div>
        </article>
      </div>
      <div class="col-lg-5" data-reveal style="--delay:.1s">
        <article class="glass feature-card feature-lg" data-tilt>
          <div class="d-flex align-items-center gap-3">
            <span class="icon-3d tone-cyan"><?= icon('target') ?></span>
            <span class="feature-tag">Adaptive</span>
          </div>
          <h3>Quizzes that find your weak spots</h3>
          <p>Every answer updates your mastery map. Topics below 60% are flagged with practice and reading picked for you.</p>
          <div class="mastery" aria-hidden="true">
            <?php foreach ([['Algebra', 86, false], ['Geometry', 72, false], ['Motion', 45, true], ['Energy', 64, false]] as [$topic, $pct, $weak]): ?>
              <div class="mastery-row">
                <span><?= e($topic) ?></span>
                <span class="mastery-track"><span class="mastery-fill<?= $weak ? ' weak' : '' ?>" style="--v:<?= $pct ?>%"></span></span>
                <span class="text-end fw-semibold"><?= $weak ? '<span class="chip-weak">' . $pct . '%</span>' : $pct . '%' ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </article>
      </div>

      <?php foreach ([
        ['chart', 'tone-indigo', 'Progress you can see', 'Score trends, recent quizzes and recommended reading on one clear dashboard.'],
        ['users', 'tone-teal', 'Study groups', 'Join or start a group for your subject, with a live chat and a seat limit the owner controls.'],
        ['book', 'tone-purple', 'Teacher resources', 'Teachers publish notes and links, and see which topics their students struggle with.'],
        ['globe', 'tone-blue', 'Bangla & English', 'Switch language any time. Every explanation adapts to the language you prefer.'],
      ] as $i => [$ic, $tone, $t, $d]): ?>
        <div class="col-md-6 col-lg-3" data-reveal style="--delay:<?= .05 * $i ?>s">
          <article class="glass feature-card" data-tilt>
            <span class="icon-3d <?= $tone ?>"><?= icon($ic) ?></span>
            <h3><?= e($t) ?></h3>
            <p><?= e($d) ?></p>
          </article>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ How it works ============ -->
<section class="section" id="how" aria-labelledby="how-title">
  <div class="container">
    <div class="section-head" data-reveal>
      <span class="eyebrow"><?= icon('layers') ?> How it works</span>
      <h2 id="how-title">From first question to <span class="text-grad">real progress</span></h2>
      <p>Three simple steps. No setup, no cost for students.</p>
    </div>
    <div class="row g-4 g-lg-5 steps">
      <?php foreach ([
        ['Create your account', 'Sign up as a student in seconds, or as a teacher. An admin checks teacher accounts before they go live.'],
        ['Learn and practise', 'Ask the AI tutor, read teacher notes and take short quizzes on the subjects you are studying.'],
        ['Track and improve', 'Your dashboard shows weak topics and what to revise next. Teachers see where the whole class needs help.'],
      ] as $i => [$t, $d]): ?>
        <div class="col-lg-4 step" data-reveal style="--delay:<?= .12 * $i ?>s">
          <div class="step-orb"><?= $i + 1 ?></div>
          <h3><?= e($t) ?></h3>
          <p><?= e($d) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ Statistics (live from the database) ============ -->
<section class="section-sm" aria-label="Platform statistics">
  <div class="container">
    <div class="glass glow-border stats-band" data-reveal>
      <div class="row g-4">
        <div class="col-6 col-md-3 stat"><div class="stat-value text-grad" data-count="<?= (int)$stats['questions'] ?>"><?= (int)$stats['questions'] ?></div><div class="stat-label">Practice questions</div></div>
        <div class="col-6 col-md-3 stat"><div class="stat-value text-grad" data-count="<?= (int)$stats['topics'] ?>"><?= (int)$stats['topics'] ?></div><div class="stat-label">Topics covered</div></div>
        <div class="col-6 col-md-3 stat"><div class="stat-value text-grad" data-count="<?= (int)$stats['subjects'] ?>"><?= (int)$stats['subjects'] ?></div><div class="stat-label">Subjects</div></div>
        <div class="col-6 col-md-3 stat"><div class="stat-value text-grad">24/7</div><div class="stat-label">AI tutor availability</div></div>
      </div>
    </div>
  </div>
</section>

<!-- ============ Testimonials ============ -->
<?php /* Sample testimonials for the design. Replace them with real, permission-given feedback before launch. */ ?>
<section class="section" aria-labelledby="voices-title">
  <div class="container">
    <div class="section-head" data-reveal>
      <span class="eyebrow"><?= icon('message') ?> Testimonials</span>
      <h2 id="voices-title">Loved by learners <span class="text-grad">and teachers</span></h2>
    </div>
    <div class="row g-4">
      <?php foreach ([
        ['NJ', 'Nusrat J.', 'Class 10 student', 'The weak-topic report showed me I kept missing questions on motion. Two weeks of targeted practice later, physics finally clicked.'],
        ['RU', 'Rahim U.', 'Physics teacher', 'I upload notes once and immediately see which topics my students struggle with. It has changed how I plan revision classes.'],
        ['AH', 'Arif H.', 'HSC candidate', 'Asking the AI tutor in Bangla made difficult concepts feel simple. It is like having a patient teacher available at midnight.'],
      ] as $i => [$ini, $name, $role, $quote]): ?>
        <div class="col-md-4 quote-col" data-reveal style="--delay:<?= .1 * $i ?>s">
          <figure class="glass quote-card m-0" data-tilt>
            <span class="quote-mark" aria-hidden="true">&ldquo;</span>
            <div class="stars" aria-label="5 out of 5"><?php for ($s = 0; $s < 5; $s++) echo icon('star', 'ic-fill'); ?></div>
            <blockquote><?= e($quote) ?></blockquote>
            <figcaption class="person">
              <span class="person-avatar" aria-hidden="true"><?= e($ini) ?></span>
              <span><strong><?= e($name) ?></strong><small><?= e($role) ?></small></span>
            </figcaption>
          </figure>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ About ============ -->
<section class="section" id="about" aria-labelledby="about-title">
  <div class="container">
    <div class="row align-items-center g-4 g-lg-5">
      <div class="col-lg-6 order-lg-2" data-reveal>
        <span class="eyebrow"><?= icon('cap') ?> About us</span>
        <h2 class="mt-3 mb-3" id="about-title" style="font-size:clamp(1.9rem,3.6vw,2.75rem);font-weight:700;line-height:1.12">
          A bridge between <span class="text-grad">AI and great teaching</span>
        </h2>
        <p class="text-body-secondary fs-5 mb-0">ShikkhaBridge AI was built so every student can get patient, personal help, whatever school they attend. AI handles the repetition and the late-night questions; teachers stay in charge of what is taught.</p>
        <ul class="value-list">
          <li><span class="icon-3d sm"><?= icon('globe') ?></span><div><h3>Bilingual by design</h3><p>Explanations in simple Bangla or English, chosen by the learner.</p></div></li>
          <li><span class="icon-3d sm tone-cyan"><?= icon('teacher') ?></span><div><h3>Teachers in the loop</h3><p>Verified teachers publish content and get insight into class progress.</p></div></li>
          <li><span class="icon-3d sm tone-teal"><?= icon('shield') ?></span><div><h3>Private and secure</h3><p>Hashed passwords, protected forms and role-based access to every portal.</p></div></li>
        </ul>
      </div>
      <div class="col-lg-6 order-lg-1" data-reveal style="--delay:.1s">
        <div class="layers" aria-hidden="true">
          <div class="layer layer-1 glass">
            <div class="layer-row mb-3"><span class="icon-3d sm tone-indigo"><?= icon('book') ?></span><div class="flex-grow-1"><div class="layer-line w-75 mb-2"></div><div class="layer-line w-50"></div></div></div>
            <div class="layer-line w-100 mb-2"></div><div class="layer-line w-75"></div>
          </div>
          <div class="layer layer-2 glass">
            <div class="layer-row mb-3"><span class="icon-3d sm tone-cyan"><?= icon('chart') ?></span><strong>Class insight</strong><span class="ms-auto chip-weak">Motion 45%</span></div>
            <div class="progress mb-2"><div class="progress-bar" style="width:78%"></div></div>
            <div class="progress"><div class="progress-bar bg-danger" style="width:45%"></div></div>
          </div>
          <div class="layer layer-3 glass glow-border">
            <div class="layer-row"><span class="icon-3d sm"><?= icon('sparkles') ?></span><div><strong>AI suggestion</strong><div class="small text-body-secondary">Review "Motion" with 5 practice questions</div></div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ CTA + Contact ============ -->
<section class="section pt-0" id="contact" aria-labelledby="contact-title">
  <div class="container">
    <div class="glass glow-border cta-panel" data-reveal>
      <div class="row g-4 g-lg-5 align-items-center">
        <div class="col-lg-7">
          <h2 id="contact-title" style="font-size:clamp(1.9rem,3.6vw,2.6rem);font-weight:700;line-height:1.15">Ready to start your <span class="text-grad">learning journey?</span></h2>
          <p class="text-body-secondary fs-5 mt-3 mb-4">Create a free student account in under a minute, or apply as a teacher to share your knowledge.</p>
          <div class="d-flex flex-wrap gap-3">
            <a class="btn btn-primary btn-lg" href="<?= $startUrl ?>"><?= $signedIn ? 'Go to my portal' : 'Start Learning' ?> <?= icon('arrow-right') ?></a>
            <?php if (!$signedIn): ?><a class="btn btn-outline-secondary btn-lg" href="<?= url('/signup?as=teacher') ?>"><?= icon('teacher') ?> I'm a teacher</a><?php endif; ?>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="glass p-4 d-grid gap-4">
            <div class="contact-item">
              <span class="icon-3d sm"><?= icon('mail') ?></span>
              <div><div class="fw-bold">Contact us</div><a href="mailto:<?= e(config('contact_email')) ?>"><?= e(config('contact_email')) ?></a></div>
            </div>
            <div class="contact-item">
              <span class="icon-3d sm tone-cyan"><?= icon('users') ?></span>
              <div><div class="fw-bold">Schools and teachers</div><span class="text-body-secondary small">Teacher accounts are verified by our admins, usually within a day.</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
