# ShikkhaBridge AI

A web platform where students get an AI tutor (Bangla or English), quizzes that find weak topics, study groups and teacher resources, split into a **student portal**, a **teacher portal** and an **admin portal**.
Built with PHP 8.1+ (OOP, mini MVC), MySQL, Bootstrap 5, vanilla JavaScript (Fetch) and Chart.js.

## Setup (XAMPP / any PHP + MySQL server)

1. Copy the `shikkhabridge` folder into `htdocs` (XAMPP) and start Apache and MySQL. Enable `mod_rewrite` (default in XAMPP) and the `curl` and `fileinfo` PHP extensions.
2. Create the database and tables: import `sql/schema.sql`, then `sql/seed.sql` (phpMyAdmin > Import).
   Upgrading a database made before the portals existed? Import `sql/migrate_portals.sql` once instead (renames `mentor` to `teacher` and adds `users.status`).
3. Put your database login in `config/config.local.php`:
   ```php
   <?php
   return [
       'db' => ['user' => 'root', 'pass' => ''],
       'gemini' => ['api_key' => 'YOUR_GEMINI_KEY'],  // free key from https://aistudio.google.com/ ; leave empty for demo answers
   ];
   ```
4. Create your admin account: `php scripts/create_admin.php "Your Name" you@example.com "StrongPassword123"`
5. Open `http://localhost/shikkhabridge/public/` in your browser.

Make sure `storage/` and `public/uploads/avatars/` are writable.

## Portals and roles

Login and signup have a **Student / Teacher** switch (`/login?as=teacher`). After login each role lands in its own portal with its own menu.
A student cannot log in through the teacher tab (and the other way round); admins can log in from either tab.

| Portal | URL | Who | What |
|---|---|---|---|
| Student | `/student/...` | `student` (and admins) | Dashboard with progress chart and weak topics, quizzes (`/student/quiz`), quiz history and CSV |
| Teacher | `/teacher/...` | `teacher` (and admins) | Dashboard showing where students struggle on the teacher's questions, my resources (add/edit/delete), question bank |
| Admin | `/admin/...` | `admin` | Approves teacher signups, suspends/reactivates accounts, changes roles, creates accounts, filters users by role, opens either portal |
| Shared | `/resources`, `/groups`, `/ai`, `/profile` | any logged-in user | Resource library, study groups, AI tutor, profile |

- **Students** sign up and start right away.
- **Teachers** sign up with status `pending` and cannot log in until an admin approves them (Admin dashboard or Users > Pending approval). An admin can also create a teacher account directly, which is active immediately.
- **Suspended** accounts are signed out on their next request and cannot log in.
- `/dashboard` still works and forwards each user to their own portal.

## Design system

A dark navy, glassmorphism theme with violet and cyan accents, layered over Bootstrap 5 (`public/assets/css/style.css`, sections 1 to 10).

- **Tokens** for both themes live at the top of the stylesheet (`--sb-*`, plus Bootstrap's `--bs-*` overrides), so every existing page picks up the look. Guests always see the dark theme; signed-in users can switch with the sun/moon button.
- **Layouts**: `views/layout.php` takes a `$shell` of `landing`, `auth` or `app` (default). The landing page (`views/home.php`) and auth pages (`views/auth/*`, `views/partials/auth_visual.php`) use full-width shells.
- **3D, used sparingly**: the hero scene (CSS 3D book, SVG graduation cap, AI core with orbit rings, floating glass cards), tilt-on-hover cards (`data-tilt`), layered cards in About, and 3D icon tiles (`.icon-3d`).
- **Motion**: reveal on scroll (`data-reveal`), count-up stats (`data-count`), animated progress bars, cross-page view transitions. Everything respects `prefers-reduced-motion`, and content stays visible without JavaScript.
- **Icons**: an inline SVG sprite (`views/partials/icons.php`), rendered with `icon('name')`.
- Landing-page testimonials are sample copy: replace them with real feedback before launch. Set `contact_email` in your config.

## Advanced features built

Admin dashboard, Chart.js charts, CSV export, password reset with one-time token, profile photo upload with type/size checks, separate student/teacher/admin portals with role-based login and teacher approval, CSRF tokens, login attempt limits, password strength rules (8+ characters with a letter and a number, checked in the browser and on the server), strict session cookies and security headers, dark mode saved per user, and a small JSON API used by Fetch (`/api/...`).

## Real-world rules in the code

- Weak topic rule: a topic with accuracy below 60% after at least 3 answers is recommended for revision (`Quiz::weakTopics`).
- Study group seat limit: an owner cannot approve more members than `max_members`, checked inside a transaction (`Group::approve`).
- One join request per student per group (UNIQUE key on `group_members`).

## Folder map

```
public/       web root: index.php (front controller), assets, uploads
app/Core/     Database, Router, Controller, Auth, Csrf, Validator
app/Models/   User, Resource, Quiz, Group, Catalog (all SQL, prepared statements)
app/Controllers/  one controller per area (StudentController, TeacherController, AdminController = portal homes)
app/Services/ GeminiService (AI)
views/        PHP templates (Bootstrap); views/student, views/teacher, views/admin per portal
sql/          schema.sql, seed.sql, migrate_portals.sql, dbdiagram.dbml
```
