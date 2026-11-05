<?php
/** Left-hand 3D panel of the auth pages. Expects $visual = ['title' => ..., 'text' => ..., 'points' => [...]]. */
?>
<div class="auth-visual" aria-hidden="true">
  <a class="sb-brand" href="<?= url('/') ?>" tabindex="-1"><?= logo_svg() ?><span>ShikkhaBridge <span style="opacity:.6">AI</span></span></a>

  <div class="auth-stack">
    <div class="stack-card s1">
      <div class="d-flex align-items-center gap-2 mb-2"><span class="icon-3d sm"><?= icon('sparkles') ?></span><strong>AI Tutor</strong></div>
      <div class="fc-sub">"Explain photosynthesis in Bangla"</div>
      <div class="typing mt-2"><i></i><i></i><i></i></div>
    </div>
    <div class="stack-card s2">
      <div class="fc-sub mb-1">Weekly progress</div>
      <div class="d-flex align-items-end justify-content-between mb-2"><span class="fc-big">+18%</span><span class="fc-up">on track</span></div>
      <div class="progress" style="--bs-progress-height:.4rem"><div class="progress-bar" style="width:74%"></div></div>
    </div>
    <div class="stack-card s3">
      <div class="d-flex align-items-center gap-2"><span class="icon-3d sm tone-cyan"><?= icon('target') ?></span><div><strong>Quiz complete</strong><div class="fc-sub">9 / 10 correct</div></div></div>
    </div>
  </div>

  <h2><?= e($visual['title']) ?></h2>
  <p class="mb-0"><?= e($visual['text']) ?></p>
  <?php if (!empty($visual['points'])): ?>
    <ul class="auth-points">
      <?php foreach ($visual['points'] as $p): ?><li><?= icon('check') ?> <?= e($p) ?></li><?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
