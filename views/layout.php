<?php
$u = Auth::user();
$shell = $shell ?? 'app';                       // 'landing' | 'auth' | 'app'
// Guests always get the signature dark theme; signed-in users keep their own choice.
$theme = $u ? ($u['dark_mode'] ? 'dark' : 'light') : 'dark';

// Each portal has its own menu. Admins also get a switcher to open the student and teacher portals.
$nav = match ($u['role'] ?? null) {
    'student' => [['/student', 'Dashboard'], ['/student/quiz', 'Quiz'], ['/resources', 'Resources'], ['/ai', 'AI Tutor'], ['/groups', 'Study groups']],
    'teacher' => [['/teacher', 'Dashboard'], ['/teacher/resources', 'My resources'], ['/teacher/questions', 'Question bank'], ['/resources', 'Library'], ['/ai', 'AI Tutor'], ['/groups', 'Study groups']],
    'admin' => [['/admin', 'Dashboard'], ['/admin/users', 'Users'], ['/resources', 'Library'], ['/groups', 'Study groups']],
    default => [['/', 'Home'], ['/#features', 'Features'], ['/#about', 'About'], ['/#contact', 'Contact']],
};
$here = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$here = '/' . trim(substr($here, strlen(base_path())), '/');
// highlight the longest menu path that matches the current page (anchor links never count as active)
$active = '';
foreach ($nav as [$path]) {
    if (str_contains($path, '#')) continue;
    $match = $path === '/' ? $here === '/' : ($here === $path || str_starts_with($here, $path . '/'));
    if ($match && strlen($path) > strlen($active)) $active = $path;
}
$portalLabel = ['student' => 'Student', 'teacher' => 'Teacher', 'admin' => 'Admin'][$u['role'] ?? ''] ?? '';
?>
<!doctype html>
<html lang="en" data-bs-theme="<?= $theme ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?= e(Csrf::token()) ?>">
  <meta name="base-url" content="<?= e(base_path()) ?>">
  <meta name="description" content="ShikkhaBridge AI: personalised, AI-powered learning with a bilingual AI tutor, smart quizzes, study groups and teacher insights.">
  <meta name="theme-color" content="#060a18">
  <title><?= e($title) ?> | <?= e(config('app_name')) ?></title>
  <link rel="icon" type="image/svg+xml" href="<?= asset('img/logo.svg') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
  <script>document.documentElement.classList.add('js');</script>
  <?php if (!empty($useChart)): ?><script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script><?php endif; ?>
</head>
<body class="d-flex flex-column min-vh-100 shell-<?= e($shell) ?>">
<?php require APP_ROOT . '/views/partials/icons.php'; ?>
<a class="visually-hidden-focusable skip-link" href="#main">Skip to content</a>

<nav class="navbar navbar-expand-lg sb-nav sticky-top" aria-label="Main">
  <div class="container">
    <a class="sb-brand me-3" href="<?= url($u ? portal_home($u) : '/') ?>">
      <?= logo_svg() ?><span>Shikkha<span class="text-grad">Bridge</span> <span class="brand-ai">AI</span></span>
    </a>
    <?php if ($portalLabel): ?><span class="badge portal-badge me-auto me-lg-2"><?= e($portalLabel) ?></span><?php endif; ?>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav <?= $u ? 'me-auto' : 'mx-lg-auto' ?> gap-lg-1">
        <?php foreach ($nav as [$path, $label]): ?>
          <li class="nav-item">
            <a class="nav-link<?= $active === $path ? ' active' : '' ?>" href="<?= url($path) ?>"<?= $active === $path ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
          </li>
        <?php endforeach; ?>
        <?php if (($u['role'] ?? '') === 'admin'): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Open portal</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="<?= url('/student') ?>"><?= icon('cap') ?> Student portal</a></li>
              <li><a class="dropdown-item" href="<?= url('/teacher') ?>"><?= icon('teacher') ?> Teacher portal</a></li>
            </ul>
          </li>
        <?php endif; ?>
      </ul>
      <div class="nav-actions d-flex flex-wrap align-items-center gap-2">
        <?php if ($u): ?>
          <button class="btn btn-ghost btn-icon" id="theme-toggle" type="button" aria-label="Switch to <?= $theme === 'dark' ? 'light' : 'dark' ?> mode" title="Toggle theme">
            <span class="theme-icon-dark"><?= icon('sun') ?></span><span class="theme-icon-light"><?= icon('moon') ?></span>
          </button>
          <div class="dropdown">
            <a class="btn btn-ghost d-flex align-items-center gap-2 px-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="<?= e(avatar_url($u)) ?>" alt="" class="nav-avatar">
              <span class="fw-semibold"><?= e($u['name']) ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li class="px-3 py-2 small text-body-secondary">Signed in as <?= e($u['email']) ?></li>
              <li><a class="dropdown-item" href="<?= url('/profile') ?>"><?= icon('user') ?> My profile</a></li>
              <?php if ($u['role'] === 'student'): ?>
                <li><a class="dropdown-item" href="<?= url('/student/quiz/history') ?>"><?= icon('history') ?> Quiz history</a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="post" action="<?= url('/logout') ?>">
                  <?= Csrf::field() ?>
                  <button class="dropdown-item" type="submit"><?= icon('logout') ?> Log out</button>
                </form>
              </li>
            </ul>
          </div>
        <?php else: ?>
          <a class="btn btn-ghost" href="<?= url('/login') ?>">Log in</a>
          <a class="btn btn-primary" href="<?= url('/signup') ?>">Get Started <?= icon('arrow-right') ?></a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<main id="main" class="sb-main flex-grow-1<?= $shell === 'app' ? ' container py-4 py-lg-5' : '' ?>">
  <?php $flashes = flash(); if ($flashes): ?>
    <div class="<?= $shell === 'app' ? '' : 'container pt-4' ?>">
      <?php foreach ($flashes as $f): ?>
        <div class="alert alert-<?= e($f['type']) ?> alert-dismissible fade show" role="alert">
          <?= $f['type'] === 'info' ? $f['msg'] : e($f['msg']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <?= $content ?>
</main>

<?php if ($shell === 'landing'): ?>
<footer class="sb-footer pt-5 pb-4 mt-5">
  <div class="container">
    <div class="row g-4 pb-4">
      <div class="col-lg-4">
        <a class="sb-brand mb-3" href="<?= url('/') ?>"><?= logo_svg(30) ?><span>Shikkha<span class="text-grad">Bridge</span> <span class="brand-ai">AI</span></span></a>
        <p class="footer-note mt-3 mb-0" style="max-width:340px">Personalised learning for every student, in Bangla and English, with teachers always in the loop.</p>
      </div>
      <div class="col-6 col-md-4 col-lg-2 offset-lg-2">
        <h3>Product</h3>
        <ul>
          <li><a href="<?= url('/#features') ?>">Features</a></li>
          <li><a href="<?= url('/#how') ?>">How it works</a></li>
          <li><a href="<?= url('/#about') ?>">About</a></li>
        </ul>
      </div>
      <div class="col-6 col-md-4 col-lg-2">
        <h3>Get started</h3>
        <ul>
          <li><a href="<?= url('/signup?as=student') ?>">Student sign up</a></li>
          <li><a href="<?= url('/signup?as=teacher') ?>">Teacher sign up</a></li>
          <li><a href="<?= url('/login') ?>">Log in</a></li>
        </ul>
      </div>
      <div class="col-6 col-md-4 col-lg-2">
        <h3>Support</h3>
        <ul>
          <li><a href="<?= url('/#contact') ?>">Contact</a></li>
          <li><a href="<?= url('/forgot-password') ?>">Reset password</a></li>
        </ul>
      </div>
    </div>
    <div class="d-flex flex-wrap justify-content-between gap-2 pt-4 border-top footer-note">
      <span>&copy; <?= date('Y') ?> <?= e(config('app_name')) ?>. All rights reserved.</span>
      <span>AI supports learning, it does not replace teachers. Always verify important answers.</span>
    </div>
  </div>
</footer>
<?php elseif ($shell === 'app'): ?>
<footer class="app-footer border-top py-3 text-center small">
  <div class="container">ShikkhaBridge AI supports learning, it does not replace teachers. AI answers can be wrong, so please verify them with your teacher or textbook.</div>
</footer>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
