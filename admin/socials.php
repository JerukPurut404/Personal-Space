<?php
require __DIR__ . '/includes/lib.php';

/* ---------- POST ---------- */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_load('socials.json');

        if (!isset($data['socials'])) {
            $data['socials'] = [];
        }

        $links = [];
        $count = (int)($_POST['link_count'] ?? 0);
        for ($i = 0; $i < $count; $i++) {
            $href    = trim((string)($_POST["href_$i"] ?? ''));
            $iconSrc = trim((string)($_POST["iconSrc_$i"] ?? ''));
            $iconAlt = trim((string)($_POST["iconAlt_$i"] ?? ''));
            if ($href === '') continue;

            if (!preg_match('#^https?://#i', $href) && !str_starts_with($href, 'mailto:')) {
                flash("Skipped invalid URL: $href", 'err');
                continue;
            }

            $links[] = [
                'href'    => $href,
                'iconSrc' => $iconSrc,
                'iconAlt' => $iconAlt,
            ];
        }

        $data['socials']['links'] = $links;
        json_save('socials.json', $data);
        flash('Social links saved.');
    } catch (Throwable $e) {
        flash($e->getMessage(), 'err');
    }
    redirect('socials.php');
}

/* ---------- render ---------- */

$data  = json_load('socials.json');
$links = $data['socials']['links'] ?? [];

admin_header('Socials');
?>
<h1>Social Links</h1>
<p class="muted">These icons appear on the home page. Icons come from <code>media/</code>.</p>

<form method="post" class="card" id="socials-form">
    <?= csrf_field() ?>
    <input type="hidden" name="link_count" id="link_count" value="<?= count($links) ?>">

    <div id="socials-rows">
        <?php foreach ($links as $i => $link): ?>
            <div class="repeater__row">
                <input type="text" name="href_<?= $i ?>" value="<?= h($link['href']) ?>" placeholder="https://… or mailto:…">
                <?= icon_select("iconSrc_$i", $link['iconSrc'] ?? '') ?>
                <input type="text" name="iconAlt_<?= $i ?>" value="<?= h($link['iconAlt'] ?? '') ?>" placeholder="alt text">
                <button type="button" class="btn btn--ghost repeater__remove" title="Remove">✕</button>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" class="btn btn--ghost repeater__add" id="add-social">+ Add link</button>
    <button class="btn">Save socials</button>
</form>

<script>
document.getElementById('add-social').addEventListener('click', function() {
    const rows = document.getElementById('socials-rows');
    const i = rows.children.length;
    const row = document.createElement('div');
    row.className = 'repeater__row';
    row.innerHTML =
        '<input type="text" name="href_' + i + '" placeholder="https://… or mailto:…">' +
        '<input type="text" name="iconSrc_' + i + '" placeholder="media/icon.svg">' +
        '<input type="text" name="iconAlt_' + i + '" placeholder="alt text">' +
        '<button type="button" class="btn btn--ghost repeater__remove" title="Remove">✕</button>';
    rows.appendChild(row);
    document.getElementById('link_count').value = rows.children.length;
});

document.getElementById('socials-rows').addEventListener('click', function(e) {
    if (e.target.classList.contains('repeater__remove')) {
        e.target.closest('.repeater__row').remove();
        const rows = document.getElementById('socials-rows');
        document.getElementById('link_count').value = rows.children.length;
        // Re-index names
        Array.from(rows.children).forEach(function(row, idx) {
            row.querySelector('[name^="href_"]').name = 'href_' + idx;
            row.querySelector('[name^="iconSrc_"]').name = 'iconSrc_' + idx;
            row.querySelector('[name^="iconAlt_"]').name = 'iconAlt_' + idx;
        });
    }
});
</script>

<?php admin_footer(); ?>