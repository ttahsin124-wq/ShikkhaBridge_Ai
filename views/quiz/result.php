<?php $pct = $attempt['total'] > 0 ? round($attempt['score'] / $attempt['total'] * 100) : 0; ?>
<div class="card mb-3"><div class="card-body text-center">
  <div class="text-body-secondary"><?= e($attempt['subject_name']) ?> &middot; <?= e(format_date($attempt['created_at'])) ?></div>
  <div class="display-4 fw-bold <?= $pct >= 60 ? 'text-success' : 'text-danger' ?>"><?= (int)$attempt['score'] ?>/<?= (int)$attempt['total'] ?></div>
  <div class="fs-5"><?= $pct ?>%</div>
  <a class="btn btn-primary mt-2" href="<?= url('/student/quiz') ?>">Take another quiz</a>
  <a class="btn btn-outline-secondary mt-2" href="<?= url('/student') ?>">My dashboard</a>
</div></div>

<?php foreach ($answers as $i => $a):
  $correctText = $a['option_' . strtolower($a['correct_option'])]; ?>
  <div class="card mb-3 border-<?= $a['is_correct'] ? 'success' : 'danger' ?>"><div class="card-body">
    <div class="d-flex justify-content-between">
      <div class="fw-semibold"><?= $i + 1 ?>. <?= e($a['question_text']) ?></div>
      <span class="badge text-bg-<?= $a['is_correct'] ? 'success' : 'danger' ?>"><?= $a['is_correct'] ? 'Correct' : 'Wrong' ?></span>
    </div>
    <div class="small text-body-secondary mb-2"><?= e($a['topic_name']) ?></div>
    <div>Your answer: <strong><?= $a['selected_option'] ? e($a['selected_option'] . '. ' . $a['option_' . strtolower($a['selected_option'])]) : 'No answer' ?></strong></div>
    <?php if (!$a['is_correct']): ?>
      <div>Correct answer: <strong><?= e($a['correct_option'] . '. ' . $correctText) ?></strong></div>
    <?php endif; ?>
    <?php if ($a['explanation']): ?><div class="small mt-1"><?= e($a['explanation']) ?></div><?php endif; ?>
    <?php if (!$a['is_correct']): ?>
      <form class="mt-2" data-ai-form action="<?= url('/api/ai/explain') ?>" data-target="#ai-<?= (int)$a['question_id'] ?>">
        <input type="hidden" name="topic" value="<?= e('Explain why the answer to this question is "' . $correctText . '": ' . $a['question_text']) ?>">
        <button class="btn btn-sm btn-outline-primary" type="submit">
          <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
          <span class="btn-label">Explain with AI</span>
        </button>
      </form>
      <div id="ai-<?= (int)$a['question_id'] ?>" class="ai-output mt-2 d-none" aria-live="polite"></div>
    <?php endif; ?>
  </div></div>
<?php endforeach; ?>
