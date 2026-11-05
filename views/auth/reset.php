<section class="auth-wrap">
  <div class="container">
    <div class="auth-panel glass-strong row g-0">
      <div class="col-lg-6 d-none d-lg-block">
        <?php $visual = [
            'title' => 'Choose a strong new password.',
            'text' => 'Once it is saved, this reset link stops working and you can log in straight away.',
            'points' => ['At least 8 characters', 'Include a letter and a number', 'Do not reuse old passwords'],
        ]; require APP_ROOT . '/views/partials/auth_visual.php'; ?>
      </div>
      <div class="col-lg-6">
        <div class="auth-form">
          <a class="auth-back" href="<?= url('/login') ?>"><?= icon('arrow-left') ?> Back to log in</a>
          <h1 class="auth-title">Set a new password</h1>
          <p class="text-body-secondary mb-4">Pick something you have not used here before.</p>
          <form method="post" action="<?= url('/reset-password/' . $token) ?>" data-validate novalidate>
            <?= Csrf::field() ?>
            <div class="mb-3">
              <label class="form-label" for="password">New password</label>
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
              <label class="form-label" for="password_confirm">Confirm new password</label>
              <div class="field">
                <?= icon('key') ?>
                <input class="form-control" type="password" id="password_confirm" name="password_confirm" required data-match="password" autocomplete="new-password" placeholder="Repeat your password">
              </div>
              <?= err($errors, 'password_confirm') ?>
            </div>
            <button class="btn btn-primary btn-lg w-100" type="submit">Save new password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
