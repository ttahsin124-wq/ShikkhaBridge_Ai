<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="card"><div class="card-body p-4">
      <h1 class="h3 mb-3">Take a quiz</h1>
      <p class="text-body-secondary">Up to <?= Quiz::QUESTIONS_PER_QUIZ ?> random questions. We use your answers to find topics to revise.</p>
      <form method="get" action="<?= url('/student/quiz/start') ?>" data-validate novalidate>
        <div class="mb-3">
          <label class="form-label" for="subject_id">Subject</label>
          <select class="form-select" id="subject_id" name="subject_id" required>
            <option value="">Choose a subject</option>
            <?php foreach ($subjects as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label" for="topic_id">Topic (optional)</label>
          <select class="form-select" id="topic_id" name="topic_id">
            <option value="0" data-subject="">All topics</option>
            <?php foreach ($topics as $t): ?>
              <option value="<?= (int)$t['id'] ?>" data-subject="<?= (int)$t['subject_id'] ?>"><?= e($t['subject_name']) ?>: <?= e($t['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <button class="btn btn-primary" type="submit">Start quiz</button>
        <a class="btn btn-link" href="<?= url('/student/quiz/history') ?>">My history</a>
      </form>
    </div></div>
  </div>
</div>
<script>
  // Show only topics of the chosen subject
  (function () {
    const subj = document.getElementById('subject_id'), topic = document.getElementById('topic_id');
    function filter() {
      topic.value = '0';
      Array.from(topic.options).forEach(o => { o.hidden = o.dataset.subject !== '' && o.dataset.subject !== subj.value; });
    }
    subj.addEventListener('change', filter); filter();
  })();
</script>
