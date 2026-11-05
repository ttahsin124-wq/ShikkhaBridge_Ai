<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="card"><div class="card-body p-4">
      <h1 class="h3 mb-3">Create a study group</h1>
      <form method="post" action="<?= url('/groups') ?>" data-validate novalidate>
        <?= Csrf::field() ?>
        <div class="mb-3">
          <label class="form-label" for="name">Group name</label>
          <input class="form-control" id="name" name="name" required maxlength="150" value="<?= old('name') ?>">
          <?= err($errors, 'name') ?>
        </div>
        <div class="mb-3">
          <label class="form-label" for="subject_id">Subject</label>
          <select class="form-select" id="subject_id" name="subject_id" required>
            <option value="">Choose a subject</option>
            <?php foreach ($subjects as $s): ?>
              <option value="<?= (int)$s['id'] ?>" <?= (int)($_SESSION['old']['subject_id'] ?? 0) === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <?= err($errors, 'subject_id') ?>
        </div>
        <div class="mb-3">
          <label class="form-label" for="description">What will you study?</label>
          <textarea class="form-control" id="description" name="description" rows="3" maxlength="500"><?= old('description') ?></textarea>
        </div>
        <div class="row">
          <div class="col-md-8 mb-3">
            <label class="form-label" for="meeting_time">Meeting time</label>
            <input class="form-control" id="meeting_time" name="meeting_time" maxlength="100" placeholder="e.g. Fridays 5 PM" value="<?= old('meeting_time') ?>">
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label" for="max_members">Max members</label>
            <input class="form-control" type="number" id="max_members" name="max_members" min="2" max="50" required value="<?= old('max_members', '10') ?>">
            <?= err($errors, 'max_members') ?>
          </div>
        </div>
        <button class="btn btn-primary" type="submit">Create group</button>
        <a class="btn btn-link" href="<?= url('/groups') ?>">Cancel</a>
      </form>
    </div></div>
  </div>
</div>
