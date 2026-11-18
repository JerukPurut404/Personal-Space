<?php
require __DIR__ . '/includes/lib.php';

/** Starter PHP page for a new post (mirrors blog/drive.php). */
function blog_php_template(string $slug, string $title): string
{
    return strtr(<<<'PHP'
<?php
require_once __DIR__ . '/../src/languages.php';
require_once __DIR__ . '/Parsedown.php';
$markdown = file_get_contents(__DIR__ . '/markdowns/{{SLUG}}.md');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{TITLE}}</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
  <div class="container">
    <header class="header">
      <a href="../index.php"><h3>Hello World</h3></a>
      <nav class="menu">
        <ul class="menu__inner">
          <li><a href="../about.php"><?php echo t('About'); ?></a></li>
          <li><a href="../projects.php"><?php echo t('Projects'); ?></a></li>
          <li><a href="../blogs.php"><?php echo t('Blogs'); ?></a></li>
        </ul>
      </nav>
    </header>
    <main class="post">
      <article class="blog__article">
        <?php echo (new Parsedown())->text($markdown); ?>
      </article>
    </main>
  </div>
</body>
</html>
PHP, ['{{SLUG}}' => $slug, '{{TITLE}}' => htmlspecialchars($title)]);
}

function blog_form(?int $i, array $item = []): void
{
    $text = $item['text'] ?? [];
    $sub  = $item['subtext'] ?? [];
    ?>
    <form method="post" class="form-grid">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="index" value="<?= $i ?? '' ?>">

        <?php if ($i === null): ?>
            <label>URL slug <span class="muted">→ blog/&lt;slug&gt;.php</span>
                <input name="slug" placeholder="my-new-post" pattern="[a-z0-9-]*">
            </label>
        <?php else: ?>
            <p class="muted">File: <code><?= h($item['href']) ?></code> ·
                <a href="post_edit.php?i=<?= $i ?>">Edit content →</a></p>
        <?php endif; ?>

        <?php foreach (LANGS as $code => $name): ?>
            <fieldset>
                <legend><?= h($name) ?><?= $code === 'en' ? ' (required)' : ' (optional)' ?></legend>
                <label>Title
                    <input name="text[<?= $code ?>]" value="<?= h($text[$code] ?? '') ?>">
                </label>
                <label>Summary
                    <textarea name="subtext[<?= $code ?>]" rows="2"><?= h($sub[$code] ?? '') ?></textarea>
                </label>
            </fieldset>
        <?php endforeach; ?>

        <label>Date
            <input type="date" name="date" value="<?= h($item['date'] ?? date('Y-m-d')) ?>" required>
        </label>
        <button class="btn">Save</button>
    </form>
    <?php
}

/* ---------- POST handling ---------- */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';
        $data   = json_load('blogs.json');
        $items  = $data['blogs']['items'];
        $i      = ($_POST['index'] ?? '') === '' ? null : (int)$_POST['index'];

        if ($action === 'save') {
            $date = (string)($_POST['date'] ?? '');
            if (!DateTime::createFromFormat('Y-m-d', $date)) {
                throw new RuntimeException('Date must be YYYY-MM-DD.');
            }
            $text = clean_multilang($_POST['text'] ?? []);
            $sub  = clean_multilang($_POST['subtext'] ?? []);
            if ($text['en'] === '') {
                throw new RuntimeException('English title is required.');
            }

            if ($i === null) {
                // New post: create the markdown file and the PHP page, then list it.
                $rawSlug = trim((string)($_POST['slug'] ?? ''));
                $slug    = slugify($rawSlug !== '' ? $rawSlug : $text['en']);
                $href    = "blog/$slug.php";
                $phpFile = ROOT_DIR . '/' . $href;
                $mdFile  = BLOG_DIR . "/markdowns/$slug.md";

                if (file_exists($phpFile) || file_exists($mdFile)) {
                    throw new RuntimeException("A post named '$slug' already exists.");
                }
                atomic_write($mdFile, "# {$text['en']}\n\nWrite your post here.\n");
                atomic_write($phpFile, blog_php_template($slug, $text['en']));

                $items[] = ['class' => 'blog__list', 'href' => $href, 'text' => $text, 'subtext' => $sub, 'date' => $date];
                flash("Created $href. Now write the content.");
            } else {
                if (!isset($items[$i])) {
                    throw new RuntimeException('Post not found.');
                }
                $items[$i] = array_merge($items[$i], ['text' => $text, 'subtext' => $sub, 'date' => $date]);
                flash('Post saved.');
            }
        } elseif ($action === 'delete' && $i !== null && isset($items[$i])) {
            $item = $items[$i];
            if (!empty($_POST['delete_files'])) {
                $md = markdown_path_for($item['href']);
                if ($md && is_file($md)) {
                    unlink($md);
                }
                $php = ROOT_DIR . '/' . $item['href'];
                if (is_valid_blog_href($item['href']) && is_file($php)) {
                    unlink($php);
                }
            }
            array_splice($items, $i, 1);
            flash('Post removed.');
        }

        $data['blogs']['items'] = array_values($items);
        json_save('blogs.json', $data);
    } catch (Throwable $e) {
        flash($e->getMessage(), 'err');
    }
    redirect('blogs.php');
}

/* ---------- render ---------- */

$items = json_load('blogs.json')['blogs']['items'];
admin_header('Blogs');
?>
<h1>Blogs</h1>
<p class="muted">Titles, summaries and dates are edited here. Article text is edited with “Edit content”.</p>

<details class="card">
    <summary>+ New post</summary>
    <?php blog_form(null); ?>
</details>

<?php foreach ($items as $i => $item): ?>
    <details class="card">
        <summary><?= h($item['text']['en']) ?> <span class="muted"><?= h($item['date']) ?></span></summary>
        <?php blog_form($i, $item); ?>
        <form method="post" class="inline-form" onsubmit="return confirm('Remove this post from the list?')">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="index" value="<?= $i ?>">
            <label class="check">
                <input type="checkbox" name="delete_files" value="1"> also delete its PHP and markdown files
            </label>
            <button class="btn btn--danger">Delete post</button>
        </form>
    </details>
<?php endforeach; ?>
<?php admin_footer();