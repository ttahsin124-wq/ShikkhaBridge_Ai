<?php $isTeacher = $portal === 'teacher'; ?>
<section class="auth-wrap">
  <div class="container">
    <div class="auth-panel glass-strong row g-0">
      <div class="col-lg-6 d-none d-lg-block">
        <?php $visual = [
            'title' => 'Start learning the way that works for you.',
            'text' => 'Join a learning platform that adapts to your pace, speaks your language and keeps your teachers in the loop.',
            'points' => ['Free for every student', 'Weak-topic detection after every quiz', 'Study groups with live chat'],
        ]; require APP_ROOT . '/views/partials/auth_visual.php'; ?>
      </div>
      <div class="col-lg-6">
        <div class="auth-form">
          <a class="auth-back" href="<?= url('/') ?>"><?= icon('arrow-left') ?> Back to home</a>
          <h1 class="auth-title">Create your account</h1>
          <p class="text-body-secondary mb-4">It takes less than a minute.</p>

          <form method="post" action="<?= url('/signup') ?>" data-validate data-role-form novalidate>
            <?= Csrf::field() ?>
            <fieldset class="mb-4">
              <legend class="form-label">Choose your role</legend>
              <div class="row g-2">
                <div class="col-6">
                  <input class="btn-check" type="radio" name="portal" id="role-student" value="student" <?= !$isTeacher ? 'checked' : '' ?>>
                  <label class="role-card" for="role-student">
                    <span class="icon-3d sm"><?= icon('cap') ?></span>
                    <span class="role-name">Student</span>
                    <span class="role-desc">Learn with the AI tutor, quizzes and groups</span>
                    <span class="role-check"><?= icon('check') ?></span>
                  </label>
                </div>
                <div class="col-6">
                  <input class="btn-check" type="radio" name="portal" id="role-teacher" value="teacher" <?= $isTeacher ? 'checked' : '' ?>>
                  <label class="role-card" for="role-teacher">
                    <span class="icon-3d sm tone-cyan"><?= icon('teacher') ?></span>
                    <span class="role-name">Teacher</span>
                    <span class="role-desc">Share resources and track student progress</span>
                    <span class="role-check"><?= icon('check') ?></span>
                  </label>
                </div>
              </div>
              <div class="role-note" data-role-show="teacher" <?= $isTeacher ? '' : 'hidden' ?>>
                <?= icon('shield') ?><span>Teacher accounts are reviewed by an admin. You can log in as soon as yours is approved.</span>
              </div>
            </fieldset>

            <div class="mb-3">
              <label class="form-label" for="name">Full name</label>
              <div class="field">
                <?= icon('user') ?>
                <input class="form-control" id="name" name="name" required maxlength="100" autocomplete="name" placeholder="Your full name" value="<?= old('name') ?>">
              </div>
              <?= err($errors, 'name') ?>
            </div>
            <div class="mb-3">
              <label class="form-label" for="email">Email</label>
              <div class="field">
                <?= icon('mail') ?>
                <input class="form-control" type="email" id="email" name="email" required maxlength="150" autocomplete="email" placeholder="you@example.com" value="<?= old('email') ?>">
              </div>
              <?= err($errors, 'email') ?>
            </div>
            <div class="mb-3">
              <label class="form-label" for="password">Password</label>
              <div class="field has-toggle">
                <?= icon('lock') ?>
                <input class="form-control" type="password" id="password" name="password" required minlength="8" data-password data-strength="pw-meter" autocomplete="new-password" placeholder="At least 8 characters" aria-describedby="pw-hint">
                <button class="pw-toggle" type="button" data-pw-toggle="password" aria-label="Show password" aria-pressed="false"><span class="ic-show"><?= icon('eye') ?></span><span class="ic-hide"><?= icon('eye-off') ?></span></button>
              </div>
              <div class="pw-meter" id="pw-meter" data-score="0" aria-hidden="true"><span></span><span></span><span></span><span></span></div>
              <div class="pw-hint" id="pw-hint">Use 8 or more characters with at least one letter and one number.</div>
              <?= err($errors, 'password') ?>
            </div>
            <div class="mb-4">
              <label class="form-label" for="password_confirm">Confirm password</label>
              <div class="field has-toggle">
                <?= icon('key') ?>
                <input class="form-control" type="password" id="password_confirm" name="password_confirm" required data-match="password" autocomplete="new-password" placeholder="Repeat your password">
                <button class="pw-toggle" type="button" data-pw-toggle="password_confirm" aria-label="Show password" aria-pressed="false"><span class="ic-show"><?= icon('eye') ?></span><span class="ic-hide"><?= icon('eye-off') ?></span></button>
              </div>
              <?= err($errors, 'password_confirm') ?>
            </div>

            <button class="btn btn-primary btn-lg w-100" type="submit">
              <span data-role-text data-student="Create student account" data-teacher="Request teacher account"><?= $isTeacher ? 'Request teacher account' : 'Create student account' ?></span> <?= icon('arrow-right') ?>
            </button>
          </form>

          <p class="auth-switch">Already have an account?
            <a href="<?= url('/login?as=' . $portal) ?>" data-role-href data-student="<?= url('/login?as=student') ?>" data-teacher="<?= url('/login?as=teacher') ?>">Log in</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</section>
