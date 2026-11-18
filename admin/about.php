<?php
require __DIR__ . '/includes/lib.php';

/* ---------- POST handling ---------- */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_load('about.json');

        $data['profile_pic'] = trim((string)($_POST['profile_pic'] ?? ''));
        $data['profile_alt'] = trim((string)($_POST['profile_alt'] ?? 'Profile Picture'));
        $data['date']        = trim((string)($_POST['date'] ?? ''));

        foreach (array_keys(LANGS) as $code) {
            $data['content_html'][$code] = (string)($_POST["content_$code"] ?? '');
        }

        json_save('about.json', $data);
        flash('About page saved.');
    } catch (Throwable $e) {
        flash($e->getMessage(), 'err');
    }
    redirect('about.php');
}

/* ---------- render ---------- */

$data = json_load('about.json');
admin_header('About');
?>
<h1>About Page</h1>
<p class="muted">Edits <code>json/about.json</code>. Content is raw HTML shown on <code>about.php</code>.</p>

<form method="post" class="card">
    <?= csrf_field() ?>

    <label>Profile Picture URL
        <input type="text" name="profile_pic" value="<?= h($data['profile_pic'] ?? '') ?>">
    </label>

    <label>Alt Text
        <input type="text" name="profile_alt" value="<?= h($data['profile_alt'] ?? '') ?>">
    </label>

    <label>Date
        <input type="text" name="date" value="<?= h($data['date'] ?? '') ?>" placeholder="11-3-2024">
    </label>

    <h2>Page Content (HTML)</h2>

    <?php foreach (LANGS as $code => $label): ?>
        <details <?= $code === 'en' ? 'open' : '' ?>>
            <summary><strong><?= h($label) ?></strong> (<?= h($code) ?>)</summary>
            <textarea name="content_<?= h($code) ?>" rows="20"
                      style="width:100%;font-family:monospace;font-size:13px;tab-size:2;"
            ><?= h($data['content_html'][$code] ?? '') ?></textarea>
        </details>
    <?php endforeach; ?>

    <button class="btn">Save about page</button>
    <a href="../about.php" target="_blank" rel="noopener" class="btn btn--ghost">Preview ↗</a>
</form>

<?php admin_footer(); ?>