<?php
require __DIR__ . '/includes/lib.php';

function resolve_media_dir(string $rel): string
{
    $base   = realpath(MEDIA_DIR);
    $target = realpath(MEDIA_DIR . '/' . $rel);
    $inside = $target !== false && ($target === $base || str_starts_with($target, $base . DIRECTORY_SEPARATOR));
    if (!$inside || !is_dir($target)) {
        throw new RuntimeException('Invalid folder.');
    }
    return $target;
}

/** Returns site files that mention $filename, so we don't delete something in use. */
function media_references(string $filename): array
{
    $files = array_merge(
        glob(JSON_DIR . '/*.json') ?: [],
        glob(ROOT_DIR . '/*.php') ?: [],
        glob(BLOG_DIR . '/*.php') ?: [],
        glob(BLOG_DIR . '/markdowns/*.md') ?: []
    );
    $hits = [];
    foreach ($files as $file) {
        if (str_contains((string)file_get_contents($file), $filename)) {
            $hits[] = substr($file, strlen(ROOT_DIR) + 1);
        }
    }
    return $hits;
}

$base = realpath(MEDIA_DIR);
try {
    $dir = resolve_media_dir(trim((string)($_REQUEST['dir'] ?? ''), '/'));
} catch (RuntimeException $e) {
    flash($e->getMessage(), 'err');
    $dir = $base;
}
$relDir = str_replace('\\', '/', trim(substr($dir, strlen($base)), DIRECTORY_SEPARATOR));
$urlSelf = 'media.php?dir=' . rawurlencode($relDir);

/* ---------- POST handling ---------- */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';

        if ($action === 'upload') {
            $saved = 0;
            $files = $_FILES['files'] ?? [];
            foreach ($files['name'] ?? [] as $k => $orig) {
                if (($files['error'][$k] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                    continue;
                }
                $tmp = $files['tmp_name'][$k];
                $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));

                if (!in_array($ext, IMAGE_EXT, true) || !is_uploaded_file($tmp)) {
                    flash("Skipped $orig (file type not allowed).", 'err');
                    continue;
                }
                if ($ext === 'svg') {
                    $svg = (string)file_get_contents($tmp);
                    if (stripos($svg, '<svg') === false || preg_match('/<script|<foreignObject|on[a-z]+\s*=|javascript:/i', $svg)) {
                        flash("Skipped $orig (SVG has no <svg> root or contains scripts).", 'err');
                        continue;
                    }
                } else {
                    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
                    if (!str_starts_with((string)$mime, 'image/')) {
                        flash("Skipped $orig (not an image).", 'err');
                        continue;
                    }
                }

                $stem = safe_filename(pathinfo($orig, PATHINFO_FILENAME));
                $dest = "$dir/$stem.$ext";
                if (file_exists($dest)) {
                    $dest = "$dir/$stem-" . bin2hex(random_bytes(2)) . ".$ext";
                }
                if (move_uploaded_file($tmp, $dest)) {
                    $saved++;
                } else {
                    flash("Could not save $orig.", 'err');
                }
            }
            flash("$saved file(s) uploaded.");

        } elseif ($action === 'mkdir') {
            $name = safe_filename((string)($_POST['folder'] ?? ''));
            if (!mkdir("$dir/$name", 0755)) {
                throw new RuntimeException('Could not create folder (it may already exist).');
            }
            flash("Folder “$name” created.");

        } elseif ($action === 'delete') {
            $name = basename((string)($_POST['file'] ?? ''));
            if (!is_file("$dir/$name")) {
                throw new RuntimeException('File not found.');
            }
            $refs = media_references($name);
            if ($refs) {
                throw new RuntimeException("$name is still used in: " . implode(', ', $refs) . '. Remove it there first.');
            }
            unlink("$dir/$name");
            flash("Deleted $name.");
        }
    } catch (Throwable $e) {
        flash($e->getMessage(), 'err');
    }
    redirect($urlSelf);
}

/* ---------- render ---------- */

$entries = array_diff(scandir($dir) ?: [], ['.', '..']);
$folders = $files = [];
foreach ($entries as $e) {
    if (is_dir("$dir/$e")) {
        $folders[] = $e;
    } elseif (is_file("$dir/$e")) {
        $files[] = $e;
    }
}
sort($folders);
sort($files);

$webPrefix = '../media/' . ($relDir !== '' ? $relDir . '/' : '');   // for <img src> from /admin
$rootPath  = 'media/'    . ($relDir !== '' ? $relDir . '/' : '');   // what "Copy path" gives you

admin_header('Media');
?>
<h1>Media</h1>
<p class="muted">
    <a href="media.php">media</a>
    <?php
    $crumb = '';
    foreach (array_filter(explode('/', $relDir)) as $seg) {
        $crumb = ltrim("$crumb/$seg", '/');
        echo ' / <a href="media.php?dir=' . rawurlencode($crumb) . '">' . h($seg) . '</a>';
    }
    ?>
</p>

<div class="card">
    <form method="post" enctype="multipart/form-data" class="form-grid">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="upload">
        <input type="file" name="files[]" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.svg">
        <button class="btn">Upload</button>
    </form>
    <form method="post" class="inline-form">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="mkdir">
        <input name="folder" placeholder="new folder name" style="width:auto">
        <button class="btn btn--ghost">Create folder</button>
    </form>
</div>

<div class="media-grid">
    <?php foreach ($folders as $f): ?>
        <a class="media-item" href="media.php?dir=<?= rawurlencode(ltrim("$relDir/$f", '/')) ?>">
            <span style="font-size:2rem">📁</span>
            <span class="media-name"><?= h($f) ?></span>
        </a>
    <?php endforeach; ?>

    <?php foreach ($files as $f): ?>
        <div class="media-item">
            <img src="<?= h($webPrefix . rawurlencode($f)) ?>" alt="" loading="lazy">
            <div class="media-name"><?= h($f) ?></div>
            <div class="row-actions" style="justify-content:center">
                <button type="button" class="btn btn--ghost" data-copy="<?= h($rootPath . $f) ?>">Copy path</button>
                <form method="post" class="inline" onsubmit="return confirm('Delete this file?')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="file" value="<?= h($f) ?>">
                    <button class="btn btn--danger">Delete</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<p class="muted">Paths are root-relative (<code>media/…</code>). From blog posts, prefix <code>../</code>.</p>
<?php admin_footer();