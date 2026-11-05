<h1 class="h3 mb-3">Quiz: <?= e($subject['name']) ?></h1>
<form method="post" action="<?= url('/student/quiz/submit') ?>" id="quiz-form">
  <?= Csrf::field() ?>
  <?php foreach ($questions as $i => $q): ?>
    <div class="card mb-3"><div class="card-body">
      <div class="fw-semibold mb-2"><?= $i + 1 ?>. <?= e($q['question_text']) ?></div>
      <?php foreach (option_letters() as $L): ?>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="answers[<?= (int)$q['id'] ?>]" id="q<?= (int)$q['id'] . $L ?>" value="<?= $L ?>">
          <label class="form-check-label" for="q<?= (int)$q['id'] . $L ?>"><?= $L ?>. <?= e($q['option_' . strtolower($L)]) ?></label>
        </div>
      <?php endforeach; ?>
    </div></div>
  <?php endforeach; ?>
  <button class="btn btn-primary btn-lg" type="submit" data-confirm-unanswered>Submit answers</button>
</form>
