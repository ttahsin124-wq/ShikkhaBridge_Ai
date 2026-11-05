<?php $editing = $r !== null; ?>
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card"><div class="card-body p-4">
      <h1 class="h3 mb-3"><?= $editing ? 'Edit resource' : 'Add a learning resource' ?></h1>
      <form method="post" action="<?= $editing ? url('/teacher/resources/' . (int)$r['id'] . '/update') : url('/teacher/resources') ?>" data-validate novalidate>
        <?= Csrf::field() ?>
        <div class="mb-3">
          <label class="form-label" for="title">Title</label>
          <input class="form-control" id="title" name="title" required maxlength="200" value="<?= old('title', $r['title'] ?? '') ?>">
          <?= err($errors, 'title') ?>
        </div>
        <div class="mb-3">
          <label class="form-label" for="subject_id">Subject</label>
          <select class="form-select" id="subject_id" name="subject_id" required>
            <option value="">Choose a subject</option>
            <?php $sel = (int)($_SESSION['old']['subject_id'] ?? $r['subject_id'] ?? 0);
            foreach ($subjects as $s): ?>
              <option value="<?= (int)$s['id'] ?>" <?= $sel === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <?= err($errors, 'subject_id') ?>
        </div>
        <div class="mb-3">
          <label class="form-label" for="description">Short description</label>
          <input class="form-control" id="description" name="description" maxlength="500" value="<?= old('description', $r['description'] ?? '') ?>">
          <?= err($errors, 'description') ?>
        </div>
        <div class="mb-3">
          <label class="form-label" for="content">Content / notes</label>
          <textarea class="form-control" id="content" name="content" rows="10" required><?= old('content', $r['content'] ?? '') ?></textarea>
          <?= err($errors, 'content') ?>
        </div>
        <div class="mb-3">
          <label class="form-label" for="url">External link (optional)</label>
          <input class="form-control" type="url" id="url" name="url" maxlength="255" placeholder="https://" value="<?= old('url', $r['url'] ?? '') ?>">
          <?= err($errors, 'url') ?>
        </div>
        <button class="btn btn-primary" type="submit"><?= $editing ? 'Save changes' : 'Publish' ?></button>
        <a class="btn btn-link" href="<?= url('/teacher/resources') ?>">Cancel</a>
      </form>
    </div></div>
  </div>
</div>
