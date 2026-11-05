<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <h1 class="h3 mb-0">Learning resources</h1>
  <?php if (Auth::canUsePortal('teacher')): ?>
    <a class="btn btn-primary" href="<?= url('/teacher/resources/create') ?>">Add resource</a>
  <?php endif; ?>
</div>

<form class="row g-2 mb-3" method="get" action="<?= url('/resources') ?>">
  <div class="col-md-6"><input class="form-control" type="search" name="q" placeholder="Search by title or description" value="<?= e($q) ?>"></div>
  <div class="col-md-4">
    <select class="form-select" name="subject_id">
      <option value="0">All subjects</option>
      <?php foreach ($subjects as $s): ?>
        <option value="<?= (int)$s['id'] ?>" <?= $subjectId === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2 d-grid"><button class="btn btn-outline-primary" type="submit">Search</button></div>
</form>

<div id="delete-alert"></div>

<?php if (!$items): ?>
  <div class="alert alert-secondary">No resources found. Try a different search.</div>
<?php else: ?>
  <div class="list-group mb-3">
    <?php foreach ($items as $r):
      $canManage = ResourceController::canManage($r); ?>
      <div class="list-group-item" data-row>
        <div class="d-flex flex-wrap justify-content-between gap-2">
          <div>
            <a class="fw-semibold" href="<?= url('/resources/' . (int)$r['id']) ?>"><?= e($r['title']) ?></a>
            <span class="badge text-bg-info ms-1"><?= e($r['subject_name']) ?></span>
            <div class="text-body-secondary small"><?= e($r['description']) ?></div>
            <div class="small text-body-secondary">By <?= e($r['author']) ?> &middot; <?= e(format_date($r['created_at'])) ?></div>
          </div>
          <?php if ($canManage): ?>
            <div class="d-flex gap-2 align-items-start">
              <a class="btn btn-sm btn-outline-secondary" href="<?= url('/teacher/resources/' . (int)$r['id'] . '/edit') ?>">Edit</a>
              <button class="btn btn-sm btn-outline-danger" type="button"
                      data-delete-url="<?= url('/teacher/resources/' . (int)$r['id'] . '/delete') ?>"
                      data-confirm="Delete this resource?">Delete</button>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?= pagination_html($pager) ?>
<?php endif; ?>
