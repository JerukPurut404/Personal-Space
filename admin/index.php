<?php
require __DIR__ . '/includes/lib.php';

$stats = [
    'Blog posts'         => count(json_load('blogs.json')['blogs']['items']),
    'Projects'           => count(json_load('projects.json')['projects']),
    'Social links'       => count(json_load('socials.json')['socials']['links']),
    'Translated strings' => count(json_load('translations.json')['en'] ?? []),
];

$warnings = [];
if (str_starts_with(ADMIN_PASSWORD_HASH, '$2y$10$PASTE')) {
    $warnings[] = 'Set ADMIN_PASSWORD_HASH in admin/includes/config.php.';
}
foreach (['json', 'blog', 'blog/markdowns', 'media'] as $dir) {
    if (!is_writable(ROOT_DIR . "/$dir")) {
        $warnings[] = "$dir/ is not writable by the web server.";
    }
}
if (!is_file(BLOG_DIR . '/Parsedown.php')) {
    $warnings[] = 'blog/Parsedown.php is missing.';
}

admin_header('Dashboard');
?>
<h1>Dashboard</h1>

<?php foreach ($warnings as $w): ?>
  <div class="flash flash--err"><?= h($w) ?></div>
<?php endforeach; ?>

<div class="stats">
  <?php foreach ($stats as $label => $n): ?>
    <div class="stat"><b><?= (int)$n ?></b><?= h($label) ?></div>
  <?php endforeach; ?>
</div>

<div class="card">
  <h3>Quick actions</h3>
  <div class="row-actions">
    <a class="btn" href="blogs.php">Write a blog post</a>
    <a class="btn btn--ghost" href="projects.php">Edit projects</a>
    <a class="btn btn--ghost" href="socials.php">Edit socials</a>
    <a class="btn btn--ghost" href="translations.php">Edit translations</a>
    <a href="about.php" class="btn btn--ghost">Edit about page</a>
    <a class="btn btn--ghost" href="media.php">Upload images</a>
  </div>
</div>
<?php admin_footer();