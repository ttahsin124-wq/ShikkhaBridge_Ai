<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <h1 class="h3 mb-0">Quiz history</h1>
  <a class="btn btn-outline-secondary" href="<?= url('/student/quiz/export.csv') ?>">Download CSV</a>
</div>
<?php if (!$attempts): ?>
  <div class="alert alert-secondary">You have not taken a quiz yet. <a href="<?= url('/student/quiz') ?>">Start one</a>.</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead><tr><th>Date</th><th>Subject</th><th>Score</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($attempts as $a): ?>
        <tr>
          <td><?= e(format_date($a['created_at'])) ?></td>
          <td><?= e($a['subject_name']) ?></td>
          <td><?= (int)$a['score'] ?>/<?= (int)$a['total'] ?></td>
          <td><a href="<?= url('/student/quiz/result/' . (int)$a['id']) ?>">View</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?= pagination_html($pager) ?>
<?php endif; ?>
