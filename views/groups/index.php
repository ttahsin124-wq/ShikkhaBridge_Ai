<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <h1 class="h3 mb-0">Study groups</h1>
  <a class="btn btn-primary" href="<?= url('/groups/create') ?>">Create a group</a>
</div>

<form class="row g-2 mb-3" method="get" action="<?= url('/groups') ?>">
  <div class="col-md-6"><input class="form-control" type="search" name="q" placeholder="Search groups" value="<?= e($q) ?>"></div>
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

<?php if (!$groups): ?>
  <div class="alert alert-secondary">No groups found. Why not create one?</div>
<?php else: ?>
  <div class="row g-3 mb-3">
    <?php foreach ($groups as $g): $full = (int)$g['member_count'] >= (int)$g['max_members']; ?>
      <div class="col-md-6 col-lg-4">
        <div class="card h-100"><div class="card-body">
          <span class="badge text-bg-info mb-1"><?= e($g['subject_name']) ?></span>
          <h2 class="h5"><a href="<?= url('/groups/' . (int)$g['id']) ?>" class="stretched-link text-decoration-none"><?= e($g['name']) ?></a></h2>
          <p class="text-body-secondary small mb-2"><?= e($g['description']) ?></p>
          <div class="small">Owner: <?= e($g['owner_name']) ?></div>
          <?php if ($g['meeting_time']): ?><div class="small">When: <?= e($g['meeting_time']) ?></div><?php endif; ?>
          <div class="small <?= $full ? 'text-danger' : '' ?>"><?= (int)$g['member_count'] ?>/<?= (int)$g['max_members'] ?> members<?= $full ? ' (full)' : '' ?></div>
        </div></div>
      </div>
    <?php endforeach; ?>
  </div>
  <?= pagination_html($pager) ?>
<?php endif; ?>
