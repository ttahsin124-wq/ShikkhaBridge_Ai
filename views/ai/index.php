<h1 class="h3 mb-1">AI Tutor</h1>
<p class="text-body-secondary">Answers use <?= Auth::user()['language_pref'] === 'en' ? 'English' : 'Bangla' ?> (change this in your profile). The AI can make mistakes, so verify important facts with your teacher or textbook.</p>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="card h-100"><div class="card-body">
      <h2 class="h5">Explain a concept</h2>
      <form data-ai-form action="<?= url('/api/ai/explain') ?>" data-target="#explain-out" novalidate>
        <div class="mb-3">
          <label class="form-label" for="explain-topic">What do you want to understand?</label>
          <textarea class="form-control" id="explain-topic" name="topic" rows="3" maxlength="1000" required placeholder="e.g. What is Newton's second law?"></textarea>
        </div>
        <button class="btn btn-primary" type="submit">
          <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
          <span class="btn-label">Explain</span>
        </button>
      </form>
      <div id="explain-out" class="ai-output mt-3 d-none" aria-live="polite"></div>
    </div></div>
  </div>
  <div class="col-lg-6">
    <div class="card h-100"><div class="card-body">
      <h2 class="h5">Make practice questions</h2>
      <form data-ai-form action="<?= url('/api/ai/practice') ?>" data-target="#practice-out" novalidate>
        <div class="mb-3">
          <label class="form-label" for="practice-topic">Topic</label>
          <input class="form-control" id="practice-topic" name="topic" maxlength="1000" required list="topic-list" placeholder="e.g. Algebra">
          <datalist id="topic-list">
            <?php foreach ($topics as $t): ?><option value="<?= e($t['subject_name'] . ': ' . $t['name']) ?>"><?php endforeach; ?>
          </datalist>
        </div>
        <button class="btn btn-primary" type="submit">
          <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
          <span class="btn-label">Create 3 questions</span>
        </button>
      </form>
      <div id="practice-out" class="ai-output mt-3 d-none" aria-live="polite"></div>
    </div></div>
  </div>
</div>
