<a class="small" href="<?= url('/groups') ?>">&larr; All groups</a>
<div class="row g-4 mt-1">
  <div class="col-lg-4">
    <div class="card mb-3"><div class="card-body">
      <span class="badge text-bg-info mb-1"><?= e($g['subject_name']) ?></span>
      <h1 class="h4"><?= e($g['name']) ?></h1>
      <p class="text-body-secondary"><?= e($g['description']) ?></p>
      <div class="small">Owner: <?= e($g['owner_name']) ?></div>
      <?php if ($g['meeting_time']): ?><div class="small">When: <?= e($g['meeting_time']) ?></div><?php endif; ?>
      <div class="small mb-3"><?= (int)$g['member_count'] ?>/<?= (int)$g['max_members'] ?> members</div>

      <?php if ($isMember): ?>
        <span class="badge text-bg-success">You are a member</span>
      <?php elseif ($membership && $membership['status'] === 'pending'): ?>
        <span class="badge text-bg-warning">Request pending</span>
      <?php elseif ($membership && $membership['status'] === 'rejected'): ?>
        <span class="badge text-bg-secondary">Request declined</span>
      <?php elseif ((int)$g['member_count'] >= (int)$g['max_members']): ?>
        <span class="badge text-bg-danger">Group is full</span>
      <?php else: ?>
        <form method="post" action="<?= url('/groups/' . (int)$g['id'] . '/join') ?>">
          <?= Csrf::field() ?>
          <button class="btn btn-primary" type="submit">Ask to join</button>
        </form>
      <?php endif; ?>

      <?php if ($isOwner || Auth::hasRole('admin')): ?>
        <form class="mt-3" method="post" action="<?= url('/groups/' . (int)$g['id'] . '/delete') ?>" onsubmit="return confirm('Delete this group and its messages?')">
          <?= Csrf::field() ?>
          <button class="btn btn-sm btn-outline-danger" type="submit">Delete group</button>
        </form>
      <?php endif; ?>
    </div></div>

    <div class="card mb-3"><div class="card-body">
      <h2 class="h6">Members</h2>
      <ul class="list-unstyled mb-0">
        <?php foreach ($members as $m): ?><li><?= e($m['name']) ?><?= (int)$m['user_id'] === (int)$g['owner_id'] ? ' (owner)' : '' ?></li><?php endforeach; ?>
      </ul>
    </div></div>

    <?php if ($isOwner && $pending): ?>
      <div class="card"><div class="card-body">
        <h2 class="h6">Join requests</h2>
        <?php foreach ($pending as $p): ?>
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span><?= e($p['name']) ?></span>
            <span class="d-flex gap-1">
              <form method="post" action="<?= url('/groups/' . (int)$g['id'] . '/requests/' . (int)$p['membership_id'] . '/approve') ?>">
                <?= Csrf::field() ?><button class="btn btn-sm btn-success" type="submit">Approve</button>
              </form>
              <form method="post" action="<?= url('/groups/' . (int)$g['id'] . '/requests/' . (int)$p['membership_id'] . '/reject') ?>">
                <?= Csrf::field() ?><button class="btn btn-sm btn-outline-secondary" type="submit">Decline</button>
              </form>
            </span>
          </div>
        <?php endforeach; ?>
      </div></div>
    <?php endif; ?>
  </div>

  <div class="col-lg-8">
    <div class="card"><div class="card-body">
      <h2 class="h5">Group chat</h2>
      <?php if (!$isMember): ?>
        <p class="text-body-secondary mb-0">Join this group to read and write messages.</p>
      <?php else: ?>
        <?php $last = $messages ? (int)end($messages)['id'] : 0; ?>
        <div id="chat" class="chat-box mb-3" data-group-id="<?= (int)$g['id'] ?>" data-last-id="<?= $last ?>" data-me="<?= (int)Auth::id() ?>">
          <?php foreach ($messages as $msg): ?>
            <div class="chat-msg <?= (int)$msg['user_id'] === Auth::id() ? 'mine' : '' ?>">
              <div class="small fw-semibold"><?= e($msg['name']) ?> <span class="text-body-secondary fw-normal"><?= e(format_date($msg['created_at'])) ?></span></div>
              <div><?= e($msg['body']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
        <form id="chat-form" class="d-flex gap-2" novalidate>
          <input class="form-control" id="chat-input" maxlength="500" placeholder="Write a message" autocomplete="off" required>
          <button class="btn btn-primary" type="submit">
            <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
            <span class="btn-label">Send</span>
          </button>
        </form>
        <div id="chat-error" class="text-danger small mt-1"></div>
      <?php endif; ?>
    </div></div>
  </div>
</div>
