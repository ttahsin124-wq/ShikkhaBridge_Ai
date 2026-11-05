<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <h1 class="h3 mb-0">Admin dashboard</h1>
  <div class="d-flex flex-wrap gap-2">
    <a class="btn btn-outline-primary" href="<?= url('/admin/users') ?>">Manage users</a>
    <a class="btn btn-outline-secondary" href="<?= url('/admin/users.csv') ?>">Export users (CSV)</a>
  </div>
</div>

<?php if ($pendingTeachers): ?>
  <div class="card border-warning mb-4"><div class="card-body">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
      <h2 class="h5 mb-0">Teachers waiting for approval <span class="badge text-bg-warning"><?= (int)$stats['pending'] ?></span></h2>
      <a class="small" href="<?= url('/admin/users?status=pending') ?>">See all</a>
    </div>
    <ul class="list-group list-group-flush">
      <?php foreach ($pendingTeachers as $t): ?>
        <li class="list-group-item px-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
          <div><strong><?= e($t['name']) ?></strong> <span class="text-body-secondary small"><?= e($t['email']) ?> &middot; <?= e(format_date($t['created_at'])) ?></span></div>
          <div class="d-flex gap-2">
            <form method="post" action="<?= url('/admin/users/' . (int)$t['id'] . '/status') ?>">
              <?= Csrf::field() ?><input type="hidden" name="status" value="active">
              <button class="btn btn-sm btn-success" type="submit">Approve</button>
            </form>
            <form method="post" action="<?= url('/admin/users/' . (int)$t['id'] . '/delete') ?>" onsubmit="return confirm('Reject and delete this teacher request?')">
              <?= Csrf::field() ?>
              <button class="btn btn-sm btn-outline-danger" type="submit">Reject</button>
            </form>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div></div>
<?php endif; ?>

<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="card h-100"><div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h2 class="h5 mb-0">Student portal</h2>
        <a class="small" href="<?= url('/student') ?>">Open portal</a>
      </div>
      <div class="row text-center g-2">
        <?php foreach ([['students', 'Students'], ['attempts', 'Quiz attempts'], ['attempts_week', 'Attempts this week']] as [$k, $label]): ?>
          <div class="col-4"><div class="fs-3 fw-bold text-primary"><?= (int)$stats[$k] ?></div><div class="text-body-secondary small"><?= e($label) ?></div></div>
        <?php endforeach; ?>
      </div>
      <a class="btn btn-sm btn-outline-primary mt-3" href="<?= url('/admin/users?role=student') ?>">Manage students</a>
    </div></div>
  </div>
  <div class="col-md-6">
    <div class="card h-100"><div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h2 class="h5 mb-0">Teacher portal</h2>
        <a class="small" href="<?= url('/teacher') ?>">Open portal</a>
      </div>
      <div class="row text-center g-2">
        <?php foreach ([['teachers', 'Active teachers'], ['resources', 'Resources'], ['questions', 'Questions']] as [$k, $label]): ?>
          <div class="col-4"><div class="fs-3 fw-bold text-primary"><?= (int)$stats[$k] ?></div><div class="text-body-secondary small"><?= e($label) ?></div></div>
        <?php endforeach; ?>
      </div>
      <a class="btn btn-sm btn-outline-primary mt-3" href="<?= url('/admin/users?role=teacher') ?>">Manage teachers</a>
    </div></div>
  </div>
</div>

<div class="row g-3 mb-4 text-center">
  <?php foreach ([['new_users', 'New users this week'], ['groups', 'Study groups'], ['pending', 'Pending approvals'], ['suspended', 'Suspended accounts']] as [$k, $label]): ?>
    <div class="col-6 col-md-3">
      <div class="card h-100"><div class="card-body">
        <div class="fs-2 fw-bold text-primary"><?= (int)$stats[$k] ?></div>
        <div class="text-body-secondary small"><?= e($label) ?></div>
      </div></div>
    </div>
  <?php endforeach; ?>
</div>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="card"><div class="card-body">
      <h2 class="h5">Quiz attempts, last 7 days</h2>
      <?php if (!$perDay): ?>
        <p class="text-body-secondary mb-0">No attempts in the last 7 days.</p>
      <?php else: ?>
        <div style="height:240px"><canvas id="perDayChart"></canvas></div>
      <?php endif; ?>
    </div></div>
  </div>
  <div class="col-lg-5">
    <div class="card"><div class="card-body">
      <h2 class="h5">Users by role</h2>
      <div style="height:240px"><canvas id="rolesChart"></canvas></div>
    </div></div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const perDay = <?= json_encode($perDay, JSON_HEX_TAG) ?>;
    const roles = <?= json_encode($roles, JSON_HEX_TAG) ?>;
    const el = document.getElementById('perDayChart');
    if (el) new Chart(el, { type: 'bar', data: { labels: perDay.map(r => r.day), datasets: [{ label: 'Attempts', data: perDay.map(r => Number(r.n)), backgroundColor: '#2f4e9a' }] },
      options: { maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } } });
    new Chart(document.getElementById('rolesChart'), { type: 'doughnut', data: { labels: roles.map(r => r.role), datasets: [{ data: roles.map(r => Number(r.n)), backgroundColor: ['#2f4e9a', '#5b8fd0', '#f59e0b'] }] },
      options: { maintainAspectRatio: false } });
  });
</script>
