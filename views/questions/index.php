<h1 class="h3 mb-3">Question bank</h1>
<div id="delete-alert"></div>
<div class="row g-4">
  <div class="col-lg-5">
    <div class="card"><div class="card-body">
      <h2 class="h5 mb-3">Add a question</h2>
      <form method="post" action="<?= url('/teacher/questions') ?>" data-validate novalidate>
        <?= Csrf::field() ?>
        <div class="mb-3">
          <label class="form-label" for="topic_id">Topic</label>
          <select class="form-select" id="topic_id" name="topic_id" required>
            <option value="">Choose a topic</option>
            <?php foreach ($topics as $t): ?>
              <option value="<?= (int)$t['id'] ?>" <?= (int)($_SESSION['old']['topic_id'] ?? 0) === (int)$t['id'] ? 'selected' : '' ?>><?= e($t['subject_name']) ?>: <?= e($t['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <?= err($errors, 'topic_id') ?>
        </div>
        <div class="mb-3">
          <label class="form-label" for="question_text">Question</label>
          <textarea class="form-control" id="question_text" name="question_text" rows="2" required maxlength="500"><?= old('question_text') ?></textarea>
          <?= err($errors, 'question_text') ?>
        </div>
        <?php foreach (['a', 'b', 'c', 'd'] as $o): ?>
          <div class="mb-2">
            <label class="form-label" for="option_<?= $o ?>">Option <?= strtoupper($o) ?></label>
            <input class="form-control" id="option_<?= $o ?>" name="option_<?= $o ?>" required maxlength="255" value="<?= old('option_' . $o) ?>">
            <?= err($errors, 'option_' . $o) ?>
          </div>
        <?php endforeach; ?>
        <div class="mb-3 mt-2">
          <label class="form-label" for="correct_option">Correct answer</label>
          <select class="form-select" id="correct_option" name="correct_option" required>
            <option value="">Choose</option>
            <?php foreach (option_letters() as $L): ?>
              <option <?= ($_SESSION['old']['correct_option'] ?? '') === $L ? 'selected' : '' ?>><?= $L ?></option>
            <?php endforeach; ?>
          </select>
          <?= err($errors, 'correct_option') ?>
        </div>
        <div class="mb-3">
          <label class="form-label" for="explanation">Explanation (optional)</label>
          <input class="form-control" id="explanation" name="explanation" maxlength="500" value="<?= old('explanation') ?>">
        </div>
        <button class="btn btn-primary" type="submit">Add question</button>
      </form>
    </div></div>
  </div>

  <div class="col-lg-7">
    <form class="mb-3" method="get" action="<?= url('/teacher/questions') ?>">
      <div class="input-group">
        <select class="form-select" name="topic_id">
          <option value="0">All topics</option>
          <?php foreach ($topics as $t): ?>
            <option value="<?= (int)$t['id'] ?>" <?= $topicId === (int)$t['id'] ? 'selected' : '' ?>><?= e($t['subject_name']) ?>: <?= e($t['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <button class="btn btn-outline-primary" type="submit">Filter</button>
      </div>
    </form>
    <?php if (!$questions): ?>
      <div class="alert alert-secondary">No questions found.</div>
    <?php else: ?>
      <div class="list-group mb-3">
        <?php foreach ($questions as $q):
          $can = Auth::hasRole('admin') || (int)$q['created_by'] === Auth::id(); ?>
          <div class="list-group-item d-flex justify-content-between gap-2" data-row>
            <div>
              <div><?= e($q['question_text']) ?></div>
              <div class="small text-body-secondary"><?= e($q['subject_name']) ?> &rsaquo; <?= e($q['topic_name']) ?> &middot; answer <?= e($q['correct_option']) ?></div>
            </div>
            <?php if ($can): ?>
              <button class="btn btn-sm btn-outline-danger align-self-start" type="button"
                      data-delete-url="<?= url('/teacher/questions/' . (int)$q['id'] . '/delete') ?>"
                      data-confirm="Delete this question?">Delete</button>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
      <?= pagination_html($pager) ?>
    <?php endif; ?>
  </div>
</div>
