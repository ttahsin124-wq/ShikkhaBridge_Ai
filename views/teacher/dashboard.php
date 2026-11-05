<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <div>
    <h1 class="h3 mb-0">Hello, <?= e(Auth::user()['name']) ?></h1>
    <p class="text-body-secondary mb-0">
      <?= $isAdminView ? 'Admin view: showing content from every teacher.' : 'Here is how students are doing with your content.' ?>
    </p>
  </div>
  <div class="d-flex gap-2">
    <a class="btn btn-primary" href="<?= url('/teacher/resources/create') ?>">Add resource</a>
    <a class="btn btn-outline-primary" href="<?= url('/teacher/questions') ?>">Add questions</a>
  </div>
</div>

<div class="row g-3 mb-4 text-center">
  <?php foreach ([
    [$counts['resources'], 'Resources published'],
    [$counts['questions'], 'Questions written'],
    [$summary['students'], 'Students who answered'],
    [$summary['answers'] ? $summary['percent'] . '%' : '–', 'Average accuracy'],
  ] as [$value, $label]): ?>
    <div class="col-6 col-md-3">
      <div class="card h-100"><div class="card-body">
        <div class="fs-2 fw-bold text-primary"><?= e($value) ?></div>
        <div class="text-body-secondary small"><?= e($label) ?></div>
      </div></div>
    </div>
  <?php endforeach; ?>
</div>

<div class="row g-3 mb-3">
  <div class="col-lg-7">
    <div class="card h-100"><div class="card-body">
      <h2 class="h5">Where students struggle</h2>
      <?php if (!$topics): ?>
        <p class="text-body-secondary mb-0">No answers yet. Once students take quizzes with your questions, topic accuracy shows up here.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead><tr><th>Topic</th><th class="text-end">Answers</th><th style="width:40%">Accuracy</th></tr></thead>
            <tbody>
            <?php foreach ($topics as $t): $pct = (int)$t['percent']; $tone = $pct < 60 ? 'danger' : ($pct < 80 ? 'warning' : 'success'); ?>
              <tr>
                <td><?= e($t['topic_name']) ?> <span class="small text-body-secondary">(<?= e($t['subject_name']) ?>)</span></td>
                <td class="text-end"><?= (int)$t['answered'] ?></td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" role="progressbar" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100" aria-label="<?= e($t['topic_name']) ?> accuracy">
                      <div class="progress-bar bg-<?= $tone ?>" style="width: <?= $pct ?>%"></div>
                    </div>
                    <span class="small"><?= $pct ?>%</span>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div></div>
  </div>
  <div class="col-lg-5">
    <div class="card h-100"><div class="card-body">
      <h2 class="h5">Most-missed questions</h2>
      <?php if (!$hardest): ?>
        <p class="text-body-secondary mb-0">Questions need at least <?= Quiz::MIN_ANSWERS ?> answers before they appear here.</p>
      <?php else: ?>
        <ul class="list-group list-group-flush">
          <?php foreach ($hardest as $h): ?>
            <li class="list-group-item px-0 d-flex justify-content-between gap-2">
              <span><?= e($h['question_text']) ?></span>
              <span class="badge text-bg-danger align-self-start"><?= (int)$h['percent'] ?>%</span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div></div>
  </div>
</div>

<div class="row g-3">
  <div class="col-md-7">
    <div class="card h-100"><div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h2 class="h5 mb-0">Recent resources</h2>
        <a class="small" href="<?= url('/teacher/resources') ?>">Manage all</a>
      </div>
      <?php if (!$recentResources): ?>
        <p class="text-body-secondary mb-0">You have not published anything yet.</p>
      <?php else: foreach ($recentResources as $r): ?>
        <div class="d-flex justify-content-between gap-2 mb-2">
          <a href="<?= url('/resources/' . (int)$r['id']) ?>"><?= e($r['title']) ?></a>
          <span class="small text-body-secondary text-nowrap"><?= e($r['subject_name']) ?></span>
        </div>
      <?php endforeach; endif; ?>
    </div></div>
  </div>
  <div class="col-md-5">
    <div class="card h-100"><div class="card-body">
      <h2 class="h5">My study groups</h2>
      <?php if (!$groups): ?>
        <p class="text-body-secondary mb-2">Start a group to guide students in your subject.</p>
        <a class="btn btn-sm btn-outline-primary" href="<?= url('/groups/create') ?>">Create a group</a>
      <?php else: foreach ($groups as $g): ?>
        <div class="mb-2"><a href="<?= url('/groups/' . (int)$g['id']) ?>"><?= e($g['name']) ?></a> <span class="small text-body-secondary">(<?= e($g['subject_name']) ?>)</span></div>
      <?php endforeach; endif; ?>
    </div></div>
  </div>
</div>
