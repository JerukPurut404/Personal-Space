<?php
require __DIR__ . '/includes/lib.php';

const GALLERY_UPLOAD_DIR = ROOT_DIR . '/media/gallery';

if (!is_dir(GALLERY_UPLOAD_DIR)) {
    @mkdir(GALLERY_UPLOAD_DIR, 0775, true);
}

/* ---------- helpers ---------- */

function handle_upload(array $file): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed. Make sure the file is under php upload limits.');
    }

    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        throw new RuntimeException('Only JPG, PNG, GIF and WEBP allowed.');
    }

    $info = @getimagesize($file['tmp_name']);
    if ($info === false) {
        throw new RuntimeException('File is not a valid image.');
    }

    $base = pathinfo($file['name'], PATHINFO_FILENAME);
    $base = safe_filename($base);
    $name = $base . '-' . bin2hex(random_bytes(3)) . '.' . $ext;
    $dest = GALLERY_UPLOAD_DIR . '/' . $name;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        throw new RuntimeException('Could not save uploaded file.');
    }
    @chmod($dest, 0644);

    return 'media/gallery/' . $name;
}

/* ---------- POST ---------- */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data   = json_load('gallery.json');
        $items  = $data['items'] ?? [];
        $action = $_POST['action'] ?? '';
        $i      = ($_POST['index'] ?? '') === '' ? null : (int)$_POST['index'];

        if ($action === 'add') {
            if (!isset($_FILES['photo'])) {
                throw new RuntimeException('No photo uploaded.');
            }
            $src = handle_upload($_FILES['photo']);
            $items[] = [
                'src'         => $src,
                'date'        => trim((string)($_POST['date'] ?? date('Y-m-d'))),
                'title'       => clean_multilang($_POST['title'] ?? []),
                'description' => clean_multilang($_POST['description'] ?? []),
            ];
            flash('Photo added.');
        } elseif ($action === 'update' && $i !== null && isset($items[$i])) {
            $items[$i]['date']        = trim((string)($_POST['date'] ?? ''));
            $items[$i]['title']       = clean_multilang($_POST['title'] ?? []);
            $items[$i]['description'] = clean_multilang($_POST['description'] ?? []);

            if (!empty($_FILES['photo']['name'])) {
                $old = $items[$i]['src'] ?? '';
                $items[$i]['src'] = handle_upload($_FILES['photo']);
                if ($old && str_starts_with($old, 'media/gallery/')) {
                    @unlink(ROOT_DIR . '/' . $old);
                }
            }
            flash('Photo updated.');
        } elseif ($action === 'delete' && $i !== null && isset($items[$i])) {
            $old = $items[$i]['src'] ?? '';
            array_splice($items, $i, 1);
            if ($old && str_starts_with($old, 'media/gallery/')) {
                @unlink(ROOT_DIR . '/' . $old);
            }
            flash('Photo deleted.');
        }

        $data['items'] = array_values($items);
        json_save('gallery.json', $data);
    } catch (Throwable $e) {
        flash($e->getMessage(), 'err');
    }
    redirect('gallery.php');
}

/* ---------- render ---------- */

$items = json_load('gallery.json')['items'] ?? [];
usort($items, fn($a, $b) => strcmp($b['date'] ?? '', $a['date'] ?? ''));

admin_header('Gallery');
?>
<h1>Gallery</h1>
<p class="muted">Shown on <code>gallery.php</code>. Uploads land in <code>media/gallery/</code>.</p>

<details class="card" open>
    <summary>+ Add new photo</summary>
    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="add">

        <label>Photo file
            <input type="file" name="photo" accept="image/*" required>
        </label>

        <label>Date
            <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
        </label>

        <h3>Title</h3>
        <?php foreach (LANGS as $code => $label): ?>
            <label><?= h($label) ?>
                <input name="title[<?= h($code) ?>]" placeholder="<?= h($label) ?>">
            </label>
        <?php endforeach; ?>

        <h3>Description</h3>
        <?php foreach (LANGS as $code => $label): ?>
            <label><?= h($label) ?>
                <textarea name="description[<?= h($code) ?>]" rows="2" placeholder="<?= h($label) ?>"></textarea>
            </label>
        <?php endforeach; ?>

        <button class="btn">Add photo</button>
    </form>
</details>

<?php foreach ($items as $i => $item): ?>
    <details class="card">
        <summary>
            <?= h($item['title']['en'] ?? '(untitled)') ?>
            <span class="muted"><?= h($item['date'] ?? '') ?></span>
        </summary>

        <div style="margin:1rem 0;">
            <img src="../<?= h($item['src']) ?>" alt="" style="max-width:300px;max-height:200px;border-radius:6px;">
        </div>

        <form method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="index" value="<?= $i ?>">

            <label>Replace photo (optional)
                <input type="file" name="photo" accept="image/*">
            </label>

            <label>Date
                <input type="date" name="date" value="<?= h($item['date'] ?? '') ?>" required>
            </label>

            <h3>Title</h3>
            <?php foreach (LANGS as $code => $label): ?>
                <label><?= h($label) ?>
                    <input name="title[<?= h($code) ?>]" value="<?= h($item['title'][$code] ?? '') ?>">
                </label>
            <?php endforeach; ?>

            <h3>Description</h3>
            <?php foreach (LANGS as $code => $label): ?>
                <label><?= h($label) ?>
                    <textarea name="description[<?= h($code) ?>]" rows="2"><?= h($item['description'][$code] ?? '') ?></textarea>
                </label>
            <?php endforeach; ?>

            <button class="btn">Save changes</button>
        </form>

        <div class="row-actions">
            <?= action_form('delete', $i, 'Delete photo', [], 'btn btn--danger', 'Delete this photo? The file will also be removed.') ?>
        </div>
    </details>
<?php endforeach; ?>

<?php admin_footer(); ?>