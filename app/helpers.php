<?php
function config(string $key, $default = null)
{
    $val = $GLOBALS['__config'];
    foreach (explode('.', $key) as $part) {
        if (!is_array($val) || !array_key_exists($part, $val)) return $default;
        $val = $val[$part];
    }
    return $val;
}

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function base_path(): string
{
    return rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
}
function url(string $path = ''): string { return base_path() . '/' . ltrim($path, '/'); }
function asset(string $path): string { return url('assets/' . ltrim($path, '/')); }

function flash(?string $type = null, ?string $msg = null): array
{
    if ($type !== null && $msg !== null) {
        $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg];
        return [];
    }
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}

function old(string $key, $default = ''): string
{
    return e($_SESSION['old'][$key] ?? $default);
}

function paginate(int $total, ?int $perPage = null): array
{
    $perPage = $perPage ?: (int)config('per_page', 10);
    $pages = max(1, (int)ceil($total / $perPage));
    $page = min(max(1, (int)($_GET['page'] ?? 1)), $pages);
    return ['page' => $page, 'pages' => $pages, 'perPage' => $perPage, 'offset' => ($page - 1) * $perPage, 'total' => $total];
}

function pagination_html(array $p): string
{
    if ($p['pages'] <= 1) return '';
    $q = $_GET;
    $html = '<nav aria-label="Pagination"><ul class="pagination justify-content-center flex-wrap">';
    for ($i = 1; $i <= $p['pages']; $i++) {
        $q['page'] = $i;
        $active = $i === $p['page'] ? ' active' : '';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="?' . e(http_build_query($q)) . '">' . $i . '</a></li>';
    }
    return $html . '</ul></nav>';
}

/** Where each role lands after logging in. */
function portal_home(?array $user): string
{
    return match ($user['role'] ?? '') {
        'admin' => '/admin',
        'teacher' => '/teacher',
        'student' => '/student',
        default => '/',
    };
}

function format_date(?string $d): string { return $d ? date('d M Y, H:i', strtotime($d)) : ''; }

function option_letters(): array { return ['A', 'B', 'C', 'D']; }

/** Renders a server-side validation message under a form field. */
function err(array $errors, string $key): string
{
    return isset($errors[$key]) ? '<div class="invalid-feedback d-block">' . e($errors[$key]) . '</div>' : '';
}

/** Inline SVG icon from the sprite in views/partials/icons.php. */
function icon(string $name, string $class = ''): string
{
    return '<svg class="ic' . ($class !== '' ? ' ' . e($class) : '') . '" aria-hidden="true" focusable="false"><use href="#i-' . e($name) . '"/></svg>';
}

/** Brand mark: a bridge arc joining two learners, under an AI spark. */
function logo_svg(int $size = 34): string
{
    return '<svg class="sb-logo" width="' . $size . '" height="' . $size . '" viewBox="0 0 32 32" aria-hidden="true" focusable="false">'
        . '<rect width="32" height="32" rx="9" fill="url(#sb-grad)"/>'
        . '<rect x=".5" y=".5" width="31" height="31" rx="8.5" fill="none" stroke="rgba(255,255,255,.28)"/>'
        . '<path d="M6.5 22.5c3.2-7.4 15.8-7.4 19 0" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round"/>'
        . '<path d="M11 22.5v-3.2M16 22.5v-5.1M21 22.5v-3.2" stroke="#fff" stroke-width="2" stroke-linecap="round" opacity=".85"/>'
        . '<path d="M16 5.5l1.1 2.6 2.6 1.1-2.6 1.1L16 12.9l-1.1-2.6-2.6-1.1 2.6-1.1z" fill="#fff"/>'
        . '</svg>';
}

function avatar_url(?array $user): string
{
    return !empty($user['avatar']) ? url('uploads/avatars/' . rawurlencode($user['avatar'])) : asset('img/avatar.svg');
}
