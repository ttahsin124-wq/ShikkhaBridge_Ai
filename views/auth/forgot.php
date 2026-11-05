<section class="auth-wrap">
  <div class="container">
    <div class="auth-panel glass-strong row g-0">
      <div class="col-lg-6 d-none d-lg-block">
        <?php $visual = [
            'title' => 'Locked out? It happens.',
            'text' => 'We will create a secure, one-time link so you can choose a new password. It expires after one hour.',
            'points' => ['One-time reset link', 'Expires in 60 minutes', 'Your progress is kept safe'],
        ]; require APP_ROOT . '/views/partials/auth_visual.php'; ?>
      </div>
      <div class="col-lg-6">
        <div class="auth-form">
          <a class="auth-back" href="<?= url('/login') ?>"><?= icon('arrow-left') ?> Back to log in</a>
          <h1 class="auth-title">Forgot password</h1>
          <p class="text-body-secondary mb-4">Enter the email you signed up with and we will create a reset link.</p>
          <form method="post" action="<?= url('/forgot-password') ?>" data-validate novalidate>
            <?= Csrf::field() ?>
            <div class="mb-4">
              <label class="form-label" for="email">Email</label>
              <div class="field">
                <?= icon('mail') ?>
                <input class="form-control" type="email" id="email" name="email" required maxlength="150" autocomplete="email" placeholder="you@example.com" value="<?= old('email') ?>">
              </div>
              <?= err($errors, 'email') ?>
            </div>
            <button class="btn btn-primary btn-lg w-100" type="submit">Create reset link <?= icon('arrow-right') ?></button>
          </form>
          <p class="auth-switch">Remembered it? <a href="<?= url('/login') ?>">Log in</a></p>
        </div>
      </div>
    </div>
  </div>
</section>
