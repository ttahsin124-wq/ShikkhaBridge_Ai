<?php
// ---------- Public + shared (any logged-in role) ----------
$router->get('/', [HomeController::class, 'index']);
$router->get('/dashboard', [HomeController::class, 'dashboard']);   // forwards to the user's own portal

// Auth (?as=student|teacher picks the portal)
$router->get('/signup', [AuthController::class, 'signupForm']);
$router->post('/signup', [AuthController::class, 'signup']);
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);
$router->get('/forgot-password', [AuthController::class, 'forgotForm']);
$router->post('/forgot-password', [AuthController::class, 'forgot']);
$router->get('/reset-password/{token}', [AuthController::class, 'resetForm']);
$router->post('/reset-password/{token}', [AuthController::class, 'reset']);

// Profile
$router->get('/profile', [ProfileController::class, 'show']);
$router->post('/profile', [ProfileController::class, 'update']);
$router->post('/profile/password', [ProfileController::class, 'changePassword']);
$router->post('/api/theme', [ProfileController::class, 'toggleTheme']);

// Resource library (read by everyone; managed from the teacher portal)
$router->get('/resources', [ResourceController::class, 'index']);
$router->get('/resources/{id}', [ResourceController::class, 'show']);

// AI tutor
$router->get('/ai', [AiController::class, 'index']);
$router->post('/api/ai/explain', [AiController::class, 'explain']);
$router->post('/api/ai/practice', [AiController::class, 'practice']);
$router->post('/api/ai/summarize', [AiController::class, 'summarize']);

// Study groups (students and teachers)
$router->get('/groups', [GroupController::class, 'index']);
$router->get('/groups/create', [GroupController::class, 'create']);
$router->post('/groups', [GroupController::class, 'store']);
$router->get('/groups/{id}', [GroupController::class, 'show']);
$router->post('/groups/{id}/join', [GroupController::class, 'join']);
$router->post('/groups/{id}/delete', [GroupController::class, 'delete']);
$router->post('/groups/{id}/requests/{mid}/approve', [GroupController::class, 'approve']);
$router->post('/groups/{id}/requests/{mid}/reject', [GroupController::class, 'reject']);
$router->get('/api/groups/{id}/messages', [GroupController::class, 'messages']);
$router->post('/api/groups/{id}/messages', [GroupController::class, 'postMessage']);

// ---------- Student portal ----------
$router->get('/student', [StudentController::class, 'dashboard']);
$router->get('/student/quiz', [QuizController::class, 'index']);
$router->get('/student/quiz/start', [QuizController::class, 'start']);
$router->post('/student/quiz/submit', [QuizController::class, 'submit']);
$router->get('/student/quiz/result/{id}', [QuizController::class, 'result']);
$router->get('/student/quiz/history', [QuizController::class, 'history']);
$router->get('/student/quiz/export.csv', [QuizController::class, 'exportCsv']);
$router->get('/api/progress', [QuizController::class, 'progressApi']);

// ---------- Teacher portal ----------
$router->get('/teacher', [TeacherController::class, 'dashboard']);
$router->get('/teacher/resources', [TeacherController::class, 'resources']);
$router->get('/teacher/resources/create', [ResourceController::class, 'create']);
$router->post('/teacher/resources', [ResourceController::class, 'store']);
$router->get('/teacher/resources/{id}/edit', [ResourceController::class, 'edit']);
$router->post('/teacher/resources/{id}/update', [ResourceController::class, 'update']);
$router->post('/teacher/resources/{id}/delete', [ResourceController::class, 'delete']);
$router->get('/teacher/questions', [QuestionController::class, 'index']);
$router->post('/teacher/questions', [QuestionController::class, 'store']);
$router->post('/teacher/questions/{id}/delete', [QuestionController::class, 'delete']);

// ---------- Admin portal (controls both portals) ----------
$router->get('/admin', [AdminController::class, 'index']);
$router->get('/admin/users', [AdminController::class, 'users']);
$router->post('/admin/users', [AdminController::class, 'createUser']);
$router->post('/admin/users/{id}/role', [AdminController::class, 'setRole']);
$router->post('/admin/users/{id}/status', [AdminController::class, 'setStatus']);
$router->post('/admin/users/{id}/delete', [AdminController::class, 'deleteUser']);
$router->get('/admin/users.csv', [AdminController::class, 'exportUsers']);
