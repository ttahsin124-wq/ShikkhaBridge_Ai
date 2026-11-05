<?php
// hidden inputs that bring the admin back to the same filtered page after an action
$keep = '<input type="hidden" name="f_q" value="' . e($f['q']) . '">'
      . '<input type="hidden" name="f_role" value="' . e($f['role']) . '">'
      . '<input type="hidden" name="f_status" value="' . e($f['status']) . '">'
      . '<input type="hidden" name="f_page" value="' . (int)$pager['page'] . '">';
$tabs = [
    ['All', [], $f['role'] === '' && $f['status'] === ''],
    ['Students', ['role' => 'student'], $f['role'] === 'student'],
    ['Teachers', ['role' => 'teacher'], $f['role'] === 'teacher'],
    ['Admins', ['role' => 'admin'], $f['role'] === 'admin'],
    ['Pending approval', ['status' => 'pending'], $f['status'] === 'pending' && $f['role'] === ''],
    ['Suspended', ['status' => 'suspended'], $f['status'] === 'suspended' && $f['role'] === ''],
];
$statusBadge = ['active' => 'success', 'pending' => 'warning', 'suspended' => 'danger'];
$addErrors = $errors;
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <h1 class="h3 mb-0">Manage users</h1>
  <div class="d-flex gap-2">
    <a class="btn btn-primary" href="#add-user">Add user</a>
    <a class="btn btn-outline-secondary" href="<?= url('/admin') ?>">Back to dashboard</a>
  </div>
</div>

<ul class="nav nav-pills flex-wrap mb-3">
  <?php foreach ($tabs as [$label, $params, $on]): ?>
    <li class="nav-item">
      <a class="nav-link<?= $on ? ' active' : '' ?>" href="<?= url('/admin/users' . ($params ? '?' . http_build_query($params) : '')) ?>"<?= $on ? ' aria-current="page"' : '' ?>>
        <?= e($label) ?>
        <?php if ($label === 'Pending approval' && $pendingCount): ?><span class="badge text-bg-warning ms-1"><?= (int)$pendingCount ?></span><?php endif; ?>
      </a>
    </li>
  <?php endforeach; ?>
</ul>

<form class="row g-2 mb-3" method="get" action="<?= url('/admin/users') ?>">
  <?php if ($f['role']): ?><input type="hidden" name="role" value="<?= e($f['role']) ?>"><?php endif; ?>
  <?php if ($f['status']): ?><input type="hidden" name="status" value="<?= e($f['status']) ?>"><?php endif; ?>
  <div class="col-md-8"><input class="form-control" type="search" name="q" placeholder="Search by name or email" value="<?= e($f['q']) ?>"></div>
  <div class="col-md-4 d-grid"><button class="btn btn-outline-primary" type="submit">Search</button></div>
</form>

<?php if (!$users): ?>
  <div class="alert alert-secondary">No users found.</div>
<?php else: ?>
<div class="table-responsive">
  <table class="table align-middle">
    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Joined</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): $self = (int)$u['id'] === Auth::id(); ?>
      <tr>
        <td><?= e($u['name']) ?><?= $self ? ' <span class="small text-body-secondary">(you)</span>' : '' ?></td>
        <td><?= e($u['email']) ?></td>
        <td>
          <form class="d-flex gap-1" method="post" action="<?= url('/admin/users/' . (int)$u['id'] . '/role') ?>">
            <?= Csrf::field() ?><?= $keep ?>
            <select class="form-select form-select-sm" name="role" aria-label="Role for <?= e($u['name']) ?>" <?= $self ? 'disabled' : '' ?>>
              <?php foreach (Auth::ROLES as $r): ?>
                <option value="<?= $r ?>" <?= $u['role'] === $r ? 'selected' : '' ?>><?= $r ?></option>
              <?php endforeach; ?>
            </select>
            <?php if (!$self): ?><button class="btn btn-sm btn-outline-primary" type="submit">Save</button><?php endif; ?>
          </form>
        </td>
        <td><span class="badge text-bg-<?= $statusBadge[$u['status']] ?? 'secondary' ?>"><?= e($u['status']) ?></span></td>
        <td class="text-nowrap"><?= e(format_date($u['created_at'])) ?></td>
        <td>
          <?php if (!$self): ?>
            <div class="d-flex gap-1 justify-content-end">
              <form method="post" action="<?= url('/admin/users/' . (int)$u['id'] . '/status') ?>">
                <?= Csrf::field() ?><?= $keep ?>
                <?php if ($u['status'] === 'active'): ?>
                  <input type="hidden" name="status" value="suspended">
                  <button class="btn btn-sm btn-outline-warning" type="submit">Suspend</button>
                <?php else: ?>
                  <input type="hidden" name="status" value="active">
                  <button class="btn btn-sm btn-success" type="submit"><?= $u['status'] === 'pending' ? 'Approve' : 'Reactivate' ?></button>
                <?php endif; ?>
              </form>
              <form method="post" action="<?= url('/admin/users/' . (int)$u['id'] . '/delete') ?>" onsubmit="return confirm('Delete this user and all their data?')">
                <?= Csrf::field() ?><?= $keep ?>
                <button class="btn btn-sm btn-outline-danger" type="submit"><?= $u['status'] === 'pending' ? 'Reject' : 'Delete' ?></button>
              </form>
            </div>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?= pagination_html($pager) ?>
<?php endif; ?>

<div class="card mt-4" id="add-user"><div class="card-body p-4">
  <h2 class="h5 mb-1">Add a user</h2>
  <p class="text-body-secondary small">Accounts you create here are active right away, so a teacher does not need to wait for approval.</p>
  <form class="row g-3" method="post" action="<?= url('/admin/users') ?>" data-validate novalidate>
    <?= Csrf::field() ?>
    <div class="col-md-6">
      <label class="form-label" for="new_name">Full name</label>
      <input class="form-control" id="new_name" name="name" required maxlength="100" value="<?= old('name') ?>">
      <?= err($addErrors, 'name') ?>
    </div>
    <div class="col-md-6">
      <label class="form-label" for="new_email">Email</label>
      <input class="form-control" type="email" id="new_email" name="email" required value="<?= old('email') ?>">
      <?= err($addErrors, 'email') ?>
    </div>
    <div class="col-md-6">
      <label class="form-label" for="new_role">Role</label>
      <select class="form-select" id="new_role" name="role" required>
        <?php $oldRole = $_SESSION['old']['role'] ?? ($f['role'] ?: 'teacher'); ?>
        <?php foreach (Auth::ROLES as $r): ?>
          <option value="<?= $r ?>" <?= $oldRole === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
        <?php endforeach; ?>
      </select>
      <?= err($addErrors, 'role') ?>
    </div>
    <div class="col-md-6">
      <label class="form-label" for="new_password">Temporary password (8+ characters, a letter and a number)</label>
      <input class="form-control" type="password" id="new_password" name="password" required minlength="8" data-password autocomplete="new-password">
      <?= err($addErrors, 'password') ?>
    </div>
    <div class="col-12"><button class="btn btn-primary" type="submit">Create account</button></div>
  </form>
</div></div>
