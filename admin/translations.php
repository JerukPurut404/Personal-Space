<?php
require __DIR__ . '/includes/lib.php';

/* ---------- POST handling ---------- */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $new = array_fill_keys(array_keys(LANGS), []);
        foreach ($_POST['rows'] ?? [] as $row) {
            if (!is_array($row)) {
                continue;
            }
            $key = trim((string)($row['key'] ?? ''));
            if ($key === '' || !empty($row['delete'])) {
                continue;
            }
            foreach (array_keys(LANGS) as $code) {
                $value = trim((string)($row[$code] ?? ''));
                if ($value !== '') {
                    $new[$code][$key] = $value;   // empty = not stored, t() falls back to English
                }
            }
        }
        json_save('translations.json', $new);
        flash('Translations saved.');
    } catch (Throwable $e) {
        flash($e->getMessage(), 'err');
    }
    redirect('translations.php');
}

/* ---------- render ---------- */

$data = json_load('translations.json');
$keys = [];
foreach (array_keys(LANGS) as $code) {
    foreach (array_keys($data[$code] ?? []) as $k) {
        $keys[(string)$k] = true;
    }
}
$keys = array_keys($keys);

admin_header('Translations');
?>
<h1>Translations</h1>
<p class="muted">
    The key is the string passed to <code>t()</code> in PHP and is locked once created.
    Leave a language empty to fall back to English. To add a string, click “+ Add string”, then use
    <code>t('Your key')</code> in the code.
</p>

<form method="post" class="card repeater">
    <?= csrf_field() ?>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Key</th>
                <?php foreach (LANGS as $name): ?><th><?= h($name) ?></th><?php endforeach; ?>
                <th></th>
            </tr>
        </thead>
        <tbody class="repeater__rows">
            <?php foreach ($keys as $n => $key): ?>
                <tr class="repeater__row">
                    <td><input name="rows[<?= $n ?>][key]" value="<?= h($key) ?>" readonly></td>
                    <?php foreach (array_keys(LANGS) as $code): ?>
                        <td><textarea name="rows[<?= $n ?>][<?= $code ?>]" rows="2"><?= h($data[$code][$key] ?? '') ?></textarea></td>
                    <?php endforeach; ?>
                    <td>
                        <label class="check"><input type="checkbox" name="rows[<?= $n ?>][delete]" value="1"> del</label>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <template>
        <tr class="repeater__row">
            <td><input name="rows[__i__][key]" placeholder="New key"></td>
            <?php foreach (array_keys(LANGS) as $code): ?>
                <td><textarea name="rows[__i__][<?= $code ?>]" rows="2"></textarea></td>
            <?php endforeach; ?>
            <td><button type="button" class="btn btn--ghost repeater__remove">✕</button></td>
        </tr>
    </template>

    <div class="row-actions">
        <button type="button" class="btn btn--ghost repeater__add">+ Add string</button>
        <button class="btn">Save translations</button>
    </div>
</form>
<?php admin_footer();