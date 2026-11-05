(function () {
  'use strict';
  const csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
  const base = (document.querySelector('meta[name="base-url"]') || {}).content || '';

  // ---------- Fetch helper (always sends the CSRF token) ----------
  async function post(url, data) {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'X-CSRF-Token': csrf, 'X-Requested-With': 'fetch', 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams(data || {}).toString()
    });
    let json;
    try { json = await res.json(); } catch (e) { json = { ok: false, message: 'Something went wrong, try again.' }; }
    if (!res.ok && json.ok !== false) json.ok = false;
    return json;
  }

  function setLoading(btn, on, text) {
    if (!btn) return;
    const spinner = btn.querySelector('.spinner-border');
    const label = btn.querySelector('.btn-label');
    btn.disabled = on;
    if (spinner) spinner.classList.toggle('d-none', !on);
    if (label) {
      if (on) { label.dataset.orig = label.textContent; if (text) label.textContent = text; }
      else if (label.dataset.orig) label.textContent = label.dataset.orig;
    }
  }

  function showAlert(container, type, msg) {
    if (!container) return;
    const d = document.createElement('div');
    d.className = 'alert alert-' + type + ' alert-dismissible fade show';
    d.setAttribute('role', 'alert');
    d.textContent = msg;
    const x = document.createElement('button');
    x.type = 'button'; x.className = 'btn-close'; x.setAttribute('data-bs-dismiss', 'alert'); x.setAttribute('aria-label', 'Close');
    d.appendChild(x);
    container.innerHTML = '';
    container.appendChild(d);
  }

  // ---------- Form checks before submit ----------
  function validateForm(form) {
    let ok = true;
    form.querySelectorAll('.invalid-feedback').forEach(n => n.remove());
    form.querySelectorAll('.is-invalid').forEach(n => n.classList.remove('is-invalid'));
    form.querySelectorAll('input, select, textarea').forEach(el => {
      if (el.disabled || el.type === 'hidden') return;
      const labelEl = el.id ? form.querySelector('label[for="' + el.id + '"]') : null;
      const name = labelEl ? labelEl.textContent.replace(/\(.*\)/, '').trim() : 'This field';
      const v = el.value.trim();
      let msg = '';
      if (el.required && v === '') msg = name + " can't be empty.";
      else if (el.type === 'email' && v && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) msg = 'Please enter a valid email address.';
      else if (el.minLength > 0 && v && v.length < el.minLength) msg = name + ' must be at least ' + el.minLength + ' characters.';
      else if (el.maxLength > 0 && v.length > el.maxLength) msg = name + ' must be at most ' + el.maxLength + ' characters.';
      else if (el.dataset.password !== undefined && v && !(/[A-Za-z]/.test(v) && /\d/.test(v))) msg = name + ' must include at least one letter and one number.';
      else if (el.dataset.match && form.elements[el.dataset.match] && el.value !== form.elements[el.dataset.match].value) msg = 'Passwords do not match.';
      else if (el.type === 'file' && el.files[0] && el.dataset.maxSize && el.files[0].size > Number(el.dataset.maxSize)) msg = 'That file is too large.';
      if (msg) {
        ok = false;
        el.classList.add('is-invalid');
        const d = document.createElement('div');
        d.className = 'invalid-feedback d-block';
        d.textContent = msg;
        el.insertAdjacentElement('afterend', d);
      }
    });
    if (!ok) { const first = form.querySelector('.is-invalid'); if (first) first.focus(); }
    return ok;
  }
  document.querySelectorAll('form[data-validate]').forEach(f => {
    f.addEventListener('submit', e => { if (!validateForm(f)) e.preventDefault(); });
  });

  // ---------- Quiz: warn about unanswered questions ----------
  const quizForm = document.getElementById('quiz-form');
  if (quizForm) {
    quizForm.addEventListener('submit', e => {
      const names = new Set(Array.from(quizForm.querySelectorAll('input[type=radio]')).map(r => r.name));
      let unanswered = 0;
      names.forEach(n => { if (!quizForm.querySelector('input[name="' + n + '"]:checked')) unanswered++; });
      if (unanswered > 0 && !confirm('You left ' + unanswered + ' question(s) unanswered. Submit anyway?')) e.preventDefault();
    });
  }

  // ---------- Fetch action 1: delete without reloading (with loading indicator) ----------
  document.addEventListener('click', async e => {
    const btn = e.target.closest('[data-delete-url]');
    if (!btn) return;
    if (!confirm(btn.dataset.confirm || 'Are you sure?')) return;
    const original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Deleting...';
    const alertBox = document.getElementById('delete-alert');
    try {
      const res = await post(btn.dataset.deleteUrl);
      if (res.ok) {
        const row = btn.closest('[data-row]');
        if (row) row.remove();
        showAlert(alertBox, 'success', res.message || 'Deleted.');
      } else {
        btn.disabled = false; btn.innerHTML = original;
        showAlert(alertBox, 'danger', res.message || 'Something went wrong, try again.');
      }
    } catch (err) {
      btn.disabled = false; btn.innerHTML = original;
      showAlert(alertBox, 'danger', 'Network problem, please try again.');
    }
  });

  // ---------- Fetch action 2: AI tutor forms ----------
  document.querySelectorAll('form[data-ai-form]').forEach(form => {
    form.addEventListener('submit', async e => {
      e.preventDefault();
      const target = document.querySelector(form.dataset.target);
      const btn = form.querySelector('button[type=submit]');
      const field = form.querySelector('[name=topic]');
      target.classList.remove('d-none', 'error');
      if (field && field.required && field.value.trim() === '') {
        target.classList.add('error');
        target.textContent = 'Please type something first.';
        return;
      }
      setLoading(btn, true, 'Thinking...');
      target.textContent = 'The AI tutor is thinking...';
      try {
        const data = Object.fromEntries(new FormData(form).entries());
        const res = await post(form.getAttribute('action'), data);
        if (res.ok) { target.textContent = res.answer; }
        else { target.classList.add('error'); target.textContent = res.message || 'Something went wrong, try again.'; }
      } catch (err) {
        target.classList.add('error');
        target.textContent = 'Network problem, please try again.';
      }
      setLoading(btn, false);
    });
  });

  // ---------- Dark mode saved per user ----------
  const themeBtn = document.getElementById('theme-toggle');
  if (themeBtn) {
    themeBtn.addEventListener('click', async () => {
      const html = document.documentElement;
      const dark = html.getAttribute('data-bs-theme') !== 'dark';
      html.setAttribute('data-bs-theme', dark ? 'dark' : 'light');
      themeBtn.setAttribute('aria-label', 'Switch to ' + (dark ? 'light' : 'dark') + ' mode');
      try { await post(base + '/api/theme', { dark: dark ? '1' : '0' }); } catch (e) { /* theme still applied locally */ }
    });
  }

  // ---------- Fetch action 3: group chat ----------
  const chat = document.getElementById('chat');
  if (chat) {
    const gid = chat.dataset.groupId;
    const me = Number(chat.dataset.me);
    let last = Number(chat.dataset.lastId) || 0;
    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-input');
    const errBox = document.getElementById('chat-error');
    const scrollDown = () => { chat.scrollTop = chat.scrollHeight; };
    scrollDown();

    function render(m) {
      const wrap = document.createElement('div');
      wrap.className = 'chat-msg' + (Number(m.user_id) === me ? ' mine' : '');
      const head = document.createElement('div');
      head.className = 'small fw-semibold';
      head.textContent = m.name + ' ';
      const time = document.createElement('span');
      time.className = 'text-body-secondary fw-normal';
      time.textContent = String(m.created_at).substring(0, 16);
      head.appendChild(time);
      const body = document.createElement('div');
      body.textContent = m.body;   // textContent keeps user text safe from XSS
      wrap.appendChild(head); wrap.appendChild(body);
      chat.appendChild(wrap);
    }

    async function poll() {
      if (document.hidden) return;
      try {
        const res = await fetch(base + '/api/groups/' + gid + '/messages?after=' + last, { headers: { 'X-Requested-With': 'fetch' } });
        const data = await res.json();
        if (data.ok && data.messages.length) {
          data.messages.forEach(m => { render(m); last = Math.max(last, Number(m.id)); });
          scrollDown();
        }
      } catch (e) { /* ignore, try again on next tick */ }
    }
    setInterval(poll, 4000);

    form.addEventListener('submit', async e => {
      e.preventDefault();
      errBox.textContent = '';
      const body = input.value.trim();
      if (body === '') { errBox.textContent = "Message can't be empty."; return; }
      const btn = form.querySelector('button[type=submit]');
      setLoading(btn, true, 'Sending');
      try {
        const res = await post(base + '/api/groups/' + gid + '/messages', { body: body });
        if (res.ok) { input.value = ''; await poll(); }
        else errBox.textContent = res.message || 'Something went wrong, try again.';
      } catch (err) {
        errBox.textContent = 'Network problem, please try again.';
      }
      setLoading(btn, false);
      input.focus();
    });
  }

  // =================== Interface: motion and micro-interactions ===================
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

  // ---------- Reveal on scroll (+ count-up numbers inside) ----------
  function countUp(el) {
    const target = Number(el.dataset.count);
    if (!target || reduceMotion) return;
    const start = performance.now(), dur = 1400;
    const tick = now => {
      const p = Math.min(1, (now - start) / dur);
      el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3))).toLocaleString();
      if (p < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  }
  const revealables = document.querySelectorAll('[data-reveal]');
  if ('IntersectionObserver' in window && !reduceMotion) {
    const io = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        entry.target.querySelectorAll('[data-count]').forEach(countUp);
        io.unobserve(entry.target);
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
    revealables.forEach(el => io.observe(el));
  } else {
    revealables.forEach(el => el.classList.add('is-visible'));
  }

  // ---------- Subtle 3D tilt on cards marked data-tilt ----------
  if (finePointer && !reduceMotion) {
    document.querySelectorAll('[data-tilt]').forEach(card => {
      const max = Number(card.dataset.tilt) || 6;   // degrees
      let frame = 0;
      card.addEventListener('pointermove', e => {
        cancelAnimationFrame(frame);
        frame = requestAnimationFrame(() => {
          const r = card.getBoundingClientRect();
          const x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
          card.classList.add('is-tilting');
          card.style.setProperty('--ry', ((x - 0.5) * max).toFixed(2) + 'deg');
          card.style.setProperty('--rx', ((0.5 - y) * max).toFixed(2) + 'deg');
          card.style.setProperty('--mx', (x * 100).toFixed(1) + '%');
          card.style.setProperty('--my', (y * 100).toFixed(1) + '%');
        });
      });
      card.addEventListener('pointerleave', () => {
        cancelAnimationFrame(frame);
        card.classList.remove('is-tilting');
        card.style.setProperty('--rx', '0deg');
        card.style.setProperty('--ry', '0deg');
      });
    });

    // ---------- Hero scene follows the pointer a little ----------
    const scene = document.querySelector('[data-parallax]');
    if (scene) {
      const stage = scene.querySelector('.scene-stage');
      let raf = 0;
      window.addEventListener('pointermove', e => {
        cancelAnimationFrame(raf);
        raf = requestAnimationFrame(() => {
          const x = e.clientX / window.innerWidth - 0.5, y = e.clientY / window.innerHeight - 0.5;
          stage.style.setProperty('--py', (x * 10).toFixed(2) + 'deg');
          stage.style.setProperty('--px', (-y * 8).toFixed(2) + 'deg');
        });
      }, { passive: true });
    }
  }

  // ---------- Show / hide password ----------
  document.querySelectorAll('[data-pw-toggle]').forEach(btn => {
    const input = document.getElementById(btn.dataset.pwToggle);
    if (!input) return;
    btn.addEventListener('click', () => {
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.setAttribute('aria-pressed', String(show));
      btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
      input.focus();
    });
  });

  // ---------- Password strength meter ----------
  document.querySelectorAll('[data-strength]').forEach(input => {
    const meter = document.getElementById(input.dataset.strength);
    const hint = document.getElementById(input.getAttribute('aria-describedby'));
    if (!meter) return;
    const labels = ['Use 8 or more characters with at least one letter and one number.', 'Weak: add more characters.', 'Fair: add a number or symbol.', 'Good password.', 'Strong password.'];
    input.addEventListener('input', () => {
      const v = input.value;
      let score = 0;
      if (v.length >= 8) score++;
      if (/[A-Za-z]/.test(v) && /\d/.test(v)) score++;
      if (/[A-Z]/.test(v) && /[a-z]/.test(v)) score++;
      if (/[^A-Za-z0-9]/.test(v) || v.length >= 14) score++;
      if (v && score === 0) score = 1;
      meter.dataset.score = String(score);
      if (hint) hint.textContent = v ? labels[score] : labels[0];
    });
  });

  // ---------- Role switch on login / signup (no reload) ----------
  document.querySelectorAll('form[data-role-form]').forEach(form => {
    const scope = form.closest('section') || document;
    const sync = () => {
      const picked = form.querySelector('input[name="portal"]:checked');
      const role = picked ? picked.value : 'student';
      scope.querySelectorAll('[data-role-text]').forEach(el => { if (el.dataset[role]) el.textContent = el.dataset[role]; });
      scope.querySelectorAll('[data-role-href]').forEach(el => { if (el.dataset[role]) el.href = el.dataset[role]; });
      scope.querySelectorAll('[data-role-show]').forEach(el => { el.hidden = el.dataset.roleShow !== role; });
    };
    form.addEventListener('change', e => { if (e.target.name === 'portal') sync(); });
    sync();
  });
})();
