<h1 class="h3 mb-3">Hello, <?= e(Auth::user()['name']) ?></h1>

<div class="row g-3 mb-3">
  <div class="col-lg-8">
    <div class="card h-100"><div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h2 class="h5 mb-0">Your quiz scores</h2>
        <a class="btn btn-sm btn-primary" href="<?= url('/student/quiz') ?>">Take a quiz</a>
      </div>
      <?php if (!$progress): ?>
        <p class="text-body-secondary mb-0">No quizzes yet. Take your first quiz to see your progress here.</p>
      <?php else: ?>
        <div style="height:260px"><canvas id="progressChart" aria-label="Quiz scores over time"></canvas></div>
        <script>
          document.addEventListener('DOMContentLoaded', function () {
            const rows = <?= json_encode($progress, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?>;
            new Chart(document.getElementById('progressChart'), {
              type: 'line',
              data: {
                labels: rows.map(r => r.created_at.substring(5, 10) + ' ' + r.subject_name),
                datasets: [{ label: 'Score %', data: rows.map(r => Number(r.percent)), borderColor: '#2f4e9a', backgroundColor: 'rgba(47, 78, 154,.15)', fill: true, tension: .3 }]
              },
              options: { maintainAspectRatio: false, scales: { y: { min: 0, max: 100 } } }
            });
          });
        </script>
      <?php endif; ?>
    </div></div>
  </div>
  <div class="col-lg-4">
    <div class="card h-100"><div class="card-body">
      <h2 class="h5">Topics to revise</h2>
      <?php if (!$weak): ?>
        <p class="text-body-secondary mb-0">No weak topics found yet. Answer at least 3 questions on a topic and we will tell you where to focus.</p>
      <?php else: ?>
        <ul class="list-group list-group-flush">
          <?php foreach ($weak as $w): ?>
            <li class="list-group-item px-0">
              <div class="d-flex justify-content-between">
                <strong><?= e($w['topic_name']) ?></strong>
                <span class="badge text-bg-danger"><?= (int)$w['percent'] ?>%</span>
              </div>
              <div class="small text-body-secondary"><?= e($w['subject_name']) ?> &middot; <?= (int)$w['correct'] ?>/<?= (int)$w['answered'] ?> correct</div>
              <a class="small" href="<?= url('/student/quiz/start?subject_id=' . (int)$w['subject_id'] . '&topic_id=' . (int)$w['id']) ?>">Practise</a> &middot;
              <a class="small" href="<?= url('/ai') ?>">Ask the AI tutor</a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div></div>
  </div>
</div>

<div class="row g-3">
  <div class="col-md-4">
    <div class="card h-100"><div class="card-body">
      <h2 class="h5">Recommended reading</h2>
      <?php if (!$recommended): ?>
        <p class="text-body-secondary mb-0">Resources for your weak topics will appear here.</p>
      <?php else: foreach ($recommended as $r): ?>
        <div class="mb-2"><a href="<?= url('/resources/' . (int)$r['id']) ?>"><?= e($r['title']) ?></a></div>
      <?php endforeach; endif; ?>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card h-100"><div class="card-body">
      <h2 class="h5">Recent quizzes</h2>
      <?php if (!$recent): ?>
        <p class="text-body-secondary mb-0">Nothing yet.</p>
      <?php else: foreach ($recent as $a): ?>
        <div class="mb-2"><a href="<?= url('/student/quiz/result/' . (int)$a['id']) ?>"><?= e($a['subject_name']) ?></a>: <?= (int)$a['score'] ?>/<?= (int)$a['total'] ?></div>
      <?php endforeach; ?>
        <a class="small" href="<?= url('/student/quiz/history') ?>">See all</a>
      <?php endif; ?>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card h-100"><div class="card-body">
      <h2 class="h5">My study groups</h2>
      <?php if (!$groups): ?>
        <p class="text-body-secondary mb-2">You have not joined a group yet.</p>
        <a class="btn btn-sm btn-outline-primary" href="<?= url('/groups') ?>">Find a group</a>
      <?php else: foreach ($groups as $g): ?>
        <div class="mb-2"><a href="<?= url('/groups/' . (int)$g['id']) ?>"><?= e($g['name']) ?></a> <span class="small text-body-secondary">(<?= e($g['subject_name']) ?>)</span></div>
      <?php endforeach; endif; ?>
    </div></div>
  </div>
</div>
