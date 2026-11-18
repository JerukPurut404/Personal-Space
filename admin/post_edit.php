<?php
require __DIR__ . '/includes/lib.php';

$i      = (int)($_REQUEST['i'] ?? -1);
$items  = json_load('blogs.json')['blogs']['items'];
$item   = $items[$i] ?? null;
$mdFile = $item ? markdown_path_for($item['href']) : null;

if (!$item || !$mdFile || !is_file($mdFile)) {
    flash('No markdown file found for this post.', 'err');
    redirect('blogs.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        atomic_write($mdFile, str_replace("\r\n", "\n", (string)($_POST['markdown'] ?? '')));
        flash('Content saved.');
    } catch (Throwable $e) {
        flash($e->getMessage(), 'err');
    }
    redirect("post_edit.php?i=$i");
}

$markdown = (string)file_get_contents($mdFile);
admin_header('Edit: ' . $item['text']['en']);
?>
<h1>Edit content</h1>
<p class="muted">
  <code><?= h(basename($mdFile)) ?></code> ·
  <a href="../<?= h($item['href']) ?>" target="_blank" rel="noopener">Open on site ↗</a> ·
  <a href="blogs.php">← All posts</a>
</p>

<form method="post" class="editor">
  <?= csrf_field() ?>
  <input type="hidden" name="i" value="<?= $i ?>">
  <textarea id="md" name="markdown" spellcheck="true"><?= h($markdown) ?></textarea>
  <div class="preview" id="preview"></div>
  <button class="btn">Save content</button>
</form>

<script>
(() => {
  const md  = document.getElementById('md');
  const pv  = document.getElementById('preview');
  const csrf = document.querySelector('input[name=csrf]').value;
  let timer;
  const render = async () => {
    const body = new FormData();
    body.append('csrf', csrf);
    body.append('markdown', md.value);
    const res = await fetch('preview.php', { method: 'POST', body });
    pv.innerHTML = await res.text();
  };
  md.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(render, 300); });
  render();
})();
</script>
<?php admin_footer();