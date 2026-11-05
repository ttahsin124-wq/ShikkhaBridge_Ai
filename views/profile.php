<h1 class="h3 mb-3">My profile</h1>
<div class="row g-4">
  <div class="col-lg-7">
    <div class="card"><div class="card-body p-4">
      <h2 class="h5 mb-3">Edit information</h2>
      <form method="post" action="<?= url('/profile') ?>" enctype="multipart/form-data" data-validate novalidate>
        <?= Csrf::field() ?>
        <div class="d-flex align-items-center gap-3 mb-3">
          <img src="<?= e(avatar_url($u = Auth::user())) ?>" alt="Your photo" class="rounded-circle" width="72" height="72" style="object-fit:cover">
          <div class="flex-grow-1">
            <label class="form-label" for="avatar">Profile photo (JPG, PNG or WebP, max 2 MB)</label>
            <input class="form-control" type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp" data-max-size="2097152">
            <?= err($errors, 'avatar') ?>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label" for="name">Full name</label>
          <input class="form-control" id="name" name="name" required maxlength="100" value="<?= old('name', $u['name']) ?>">
          <?= err($errors, 'name') ?>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input class="form-control" value="<?= e($u['email']) ?>" disabled>
        </div>
        <div class="mb-3">
          <label class="form-label" for="language_pref">AI tutor language</label>
          <select class="form-select" id="language_pref" name="language_pref">
            <option value="bn" <?= $u['language_pref'] === 'bn' ? 'selected' : '' ?>>Bangla</option>
            <option value="en" <?= $u['language_pref'] === 'en' ? 'selected' : '' ?>>English</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label" for="bio">About me</label>
          <textarea class="form-control" id="bio" name="bio" rows="3" maxlength="500"><?= old('bio', $u['bio'] ?? '') ?></textarea>
          <?= err($errors, 'bio') ?>
        </div>
        <button class="btn btn-primary" type="submit">Save changes</button>
      </form>
    </div></div>
  </div>
  <div class="col-lg-5">
    <div class="card"><div class="card-body p-4">
      <h2 class="h5 mb-3">Change password</h2>
      <form method="post" action="<?= url('/profile/password') ?>" data-validate novalidate>
        <?= Csrf::field() ?>
        <div class="mb-3">
          <label class="form-label" for="current_password">Current password</label>
          <input class="form-control" type="password" id="current_password" name="current_password" required>
          <?= err($errors, 'current_password') ?>
        </div>
        <div class="mb-3">
          <label class="form-label" for="new_password">New password (8+ characters, a letter and a number)</label>
          <input class="form-control" type="password" id="new_password" name="new_password" required minlength="8" data-password>
          <?= err($errors, 'new_password') ?>
        </div>
        <div class="mb-3">
          <label class="form-label" for="new_password_confirm">Confirm new password</label>
          <input class="form-control" type="password" id="new_password_confirm" name="new_password_confirm" required data-match="new_password">
          <?= err($errors, 'new_password_confirm') ?>
        </div>
        <button class="btn btn-outline-primary" type="submit">Change password</button>
      </form>
    </div></div>
  </div>
</div>
