<?php $isTeacher = $portal === 'teacher'; ?>
<section class="auth-wrap">
  <div class="container">
    <div class="auth-panel glass-strong row g-0">
      <div class="col-lg-6 d-none d-lg-block">
        <?php $visual = [
            'title' => 'Welcome back to smarter learning.',
            'text' => 'Pick up exactly where you left off. Your progress, weak topics and study groups are waiting.',
            'points' => ['Personal AI tutor in Bangla & English', 'Quizzes that adapt to you', 'Separate portals for students and teachers'],
        ]; require APP_ROOT . '/views/partials/auth_visual.php'; ?>
      </div>
      <div class="col-lg-6">
        <div class="auth-form">
          <a class="auth-back" href="<?= url('/') ?>"><?= icon('arrow-left') ?> Back to home</a>
          <h1 class="auth-title">Log in</h1>
          <p class="text-body-secondary mb-4">Choose your portal and sign in to continue.</p>

          <form method="post" action="<?= url('/login') ?>" data-validate data-role-form novalidate>
            <?= Csrf::field() ?>
            <fieldset class="mb-4">
              <legend class="form-label">I am a</legend>
              <div class="segmented">
                <input class="btn-check" type="radio" name="portal" id="as-student" value="student" <?= !$isTeacher ? 'checked' : '' ?>>
                <label class="seg-btn" for="as-student"><?= icon('cap') ?> Student</label>
                <input class="btn-check" type="radio" name="portal" id="as-teacher" value="teacher" <?= $isTeacher ? 'checked' : '' ?>>
                <label class="seg-btn" for="as-teacher"><?= icon('teacher') ?> Teacher</label>
              </div>
              <div class="form-text">Admins can sign in with either option.</div>
            </fieldset>

            <div class="mb-3">
              <label class="form-label" for="email">Email</label>
              <div class="field">
                <?= icon('mail') ?>
                <input class="form-control" type="email" id="email" name="email" required maxlength="150" autocomplete="email" placeholder="you@example.com" value="<?= old('email') ?>">
              </div>
              <?= err($errors, 'email') ?>
            </div>
            <div class="mb-2">
              <label class="form-label" for="password">Password</label>
              <div class="field has-toggle">
                <?= icon('lock') ?>
                <input class="form-control" type="password" id="password" name="password" required autocomplete="current-password" placeholder="Your password">
                <button class="pw-toggle" type="button" data-pw-toggle="password" aria-label="Show password" aria-pressed="false"><span class="ic-show"><?= icon('eye') ?></span><span class="ic-hide"><?= icon('eye-off') ?></span></button>
              </div>
              <?= err($errors, 'password') ?>
            </div>
            <div class="d-flex justify-content-end mb-4">
              <a class="small" href="<?= url('/forgot-password') ?>">Forgot password?</a>
            </div>

            <button class="btn btn-primary btn-lg w-100" type="submit">
              Log in as <span data-role-text data-student="student" data-teacher="teacher"><?= e($portal) ?></span> <?= icon('arrow-right') ?>
            </button>
          </form>

          <p class="auth-switch">New to ShikkhaBridge?
            <a href="<?= url('/signup?as=' . $portal) ?>" data-role-href data-student="<?= url('/signup?as=student') ?>" data-teacher="<?= url('/signup?as=teacher') ?>">Create an account</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</section>
