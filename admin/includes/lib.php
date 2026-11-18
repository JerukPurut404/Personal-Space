<?php
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name('cms_session');   // separate from the public site session
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Strict',
        'secure'   => !empty($_SERVER['HTTPS']),
    ]);
    session_start();
}

/* ---------- helpers ---------- */

function h(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function flash(string $msg, string $type = 'ok'): void
{
    $_SESSION['flash'][] = [$msg, $type];
}

function take_flashes(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function slugify(string $text): string
{
    $slug = trim((string)preg_replace('/[^a-z0-9]+/', '-', strtolower($text)), '-');
    return $slug !== '' ? $slug : 'post';
}

function safe_filename(string $name): string
{
    $name = (string)preg_replace('/[^A-Za-z0-9._-]+/', '-', basename($name));
    return ltrim($name, '.') ?: 'file';
}

/** ['en' => ..., 'nl' => ..., 'id' => ...], empty languages fall back to English. */
function clean_multilang(array $input): array
{
    $en  = trim((string)($input['en'] ?? ''));
    $out = [];
    foreach (array_keys(LANGS) as $code) {
        $value = trim((string)($input[$code] ?? ''));
        $out[$code] = $value !== '' ? $value : $en;
    }
    return $out;
}

/* ---------- file I/O ---------- */

function atomic_write(string $path, string $content): void
{
    $tmp = $path . '.' . bin2hex(random_bytes(4)) . '.tmp';
    if (file_put_contents($tmp, $content, LOCK_EX) === false || !rename($tmp, $path)) {
        @unlink($tmp);
        throw new RuntimeException('Could not write ' . basename($path) . '. Check folder permissions.');
    }
}

function json_load(string $file): array
{
    $data = json_decode((string)@file_get_contents(JSON_DIR . '/' . $file), true);
    if (!is_array($data)) {
        throw new RuntimeException("Could not parse json/$file");
    }
    return $data;
}

function json_save(string $file, array $data): void
{
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    atomic_write(JSON_DIR . '/' . $file, $json);
}

/* ---------- blog helpers ---------- */

function is_valid_blog_href(string $href): bool
{
    return (bool)preg_match('#^blog/[a-z0-9-]+\.php$#', $href);
}

/** Finds the markdown file a blog PHP page renders (looks for markdowns/<name>.md). */
function markdown_path_for(string $href): ?string
{
    $php = ROOT_DIR . '/' . $href;
    if (!is_valid_blog_href($href) || !is_file($php)) {
        return null;
    }
    if (preg_match('#markdowns/([\w.-]+\.md)#', (string)file_get_contents($php), $m)) {
        return BLOG_DIR . '/markdowns/' . $m[1];
    }
    return null;
}

/* ---------- UI helpers ---------- */

function icon_select(string $name, string $current, string $prefix = ''): string
{
    $values = [];
    $files = array_merge(
        glob(MEDIA_DIR . '/*.svg') ?: [],
        glob(MEDIA_DIR . '/*.png') ?: []
    );
    sort($files);
    foreach ($files as $file) {
        $values[] = $prefix . basename($file);
    }
    if ($current !== '' && !in_array($current, $values, true)) {
        $values[] = $current;
    }
    $html = '<select name="' . h($name) . '">';
    foreach ($values as $v) {
        $html .= '<option value="' . h($v) . '"' . ($v === $current ? ' selected' : '') . '>' . h($v) . '</option>';
    }
    return $html . '</select>';
}

function csrf_token(): string
{
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . h(csrf_token()) . '">';
}

function action_form(string $action, int $index, string $label, array $extra = [], string $class = 'btn btn--ghost', string $confirm = ''): string
{
    $hidden = '';
    foreach ($extra as $k => $v) {
        $hidden .= '<input type="hidden" name="' . h($k) . '" value="' . h($v) . '">';
    }
    $onsubmit = $confirm !== '' ? ' onsubmit="return confirm(' . h(json_encode($confirm)) . ')"' : '';
    return '<form method="post" class="inline"' . $onsubmit . '>' . csrf_field()
        . '<input type="hidden" name="action" value="' . h($action) . '">'
        . '<input type="hidden" name="index" value="' . $index . '">' . $hidden
        . '<button class="' . h($class) . '">' . h($label) . '</button></form>';
}

function admin_header(string $title): void
{
    $nav = [
        'index.php'        => 'Dashboard',
        'about.php'        => 'About',
        'blogs.php'        => 'Blogs',
        'projects.php'     => 'Projects',
        'gallery.php'      => 'Gallery',
        'socials.php'      => 'Socials',
        'translations.php' => 'Translations',
        'media.php'        => 'Media',
    ];
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title><?= h($title) ?> · CMS</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
<nav class="cms-nav">
  <strong>CMS</strong>
  <?php foreach ($nav as $file => $label): ?>
    <a href="<?= $file ?>"><?= h($label) ?></a>
  <?php endforeach; ?>
  <a href="../index.php" target="_blank" rel="noopener">View site ↗</a>
  <a href="logout.php" class="right">Log out</a>
</nav>
<main class="cms-main">
  <?php foreach (take_flashes() as [$msg, $type]): ?>
    <div class="flash flash--<?= h($type) ?>"><?= h($msg) ?></div>
  <?php endforeach; ?>
    <?php
}

function admin_footer(): void
{
    ?>
</main>
<script src="admin.js"></script>
</body>
</html>
    <?php
}

/* ---------- auth ---------- */

function is_logged_in(): bool
{
    return !empty($_SESSION['admin']);
}

function login(string $password): bool
{
    if (!password_verify($password, ADMIN_PASSWORD_HASH)) {
        return false;
    }
    session_regenerate_id(true);
    $_SESSION['admin'] = true;
    return true;
}

/* ---------- gatekeeper: every page includes this file ---------- */

if (!defined('CMS_PUBLIC') && !is_logged_in()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals(csrf_token(), (string)($_POST['csrf'] ?? ''))) {
        http_response_code(403);
        exit('Invalid or expired form token. Reload the page and try again.');
    }
}