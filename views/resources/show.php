<div class="row justify-content-center">
  <div class="col-lg-9">
    <a class="small" href="<?= url('/resources') ?>">&larr; All resources</a>
    <div class="card mt-2"><div class="card-body p-4">
      <div class="d-flex flex-wrap justify-content-between gap-2">
        <div>
          <h1 class="h3"><?= e($r['title']) ?></h1>
          <span class="badge text-bg-info"><?= e($r['subject_name']) ?></span>
          <span class="small text-body-secondary ms-1">By <?= e($r['author']) ?> &middot; <?= e(format_date($r['created_at'])) ?></span>
        </div>
        <?php if ($canManage): ?>
          <div class="d-flex gap-2 align-items-start">
            <a class="btn btn-sm btn-outline-secondary" href="<?= url('/teacher/resources/' . (int)$r['id'] . '/edit') ?>">Edit</a>
            <form method="post" action="<?= url('/teacher/resources/' . (int)$r['id'] . '/delete') ?>" onsubmit="return confirm('Delete this resource?')">
              <?= Csrf::field() ?>
              <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
            </form>
          </div>
        <?php endif; ?>
      </div>
      <?php if ($r['description']): ?><p class="lead mt-3"><?= e($r['description']) ?></p><?php endif; ?>
      <div class="content-text mt-3"><?= e($r['content']) ?></div>
      <?php if ($r['url']): ?>
        <p class="mt-3"><a href="<?= e($r['url']) ?>" target="_blank" rel="noopener noreferrer">Open external link</a></p>
      <?php endif; ?>
    </div></div>

    <div class="card mt-3"><div class="card-body">
      <h2 class="h5">Summarize with AI</h2>
      <form data-ai-form action="<?= url('/api/ai/summarize') ?>" data-target="#ai-out">
        <input type="hidden" name="resource_id" value="<?= (int)$r['id'] ?>">
        <button class="btn btn-outline-primary" type="submit">
          <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
          <span class="btn-label">Summarize this resource</span>
        </button>
      </form>
      <div id="ai-out" class="ai-output mt-3 d-none" aria-live="polite"></div>
      <p class="small text-body-secondary mt-2 mb-0">AI summaries can contain mistakes. Check them against the original text.</p>
    </div></div>
  </div>
</div>
