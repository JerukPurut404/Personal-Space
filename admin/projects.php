<?php
require __DIR__ . '/includes/lib.php';

function clean_links(array $rows): array
{
    $out = [];
    foreach ($rows as $r) {
        if (!is_array($r)) {
            continue;
        }
        $url = trim((string)($r['url'] ?? ''));
        if ($url === '') {
            continue;
        }
        if (!preg_match('#^https?://#i', $url)) {
            throw new RuntimeException("Link must start with https:// (got: $url)");
        }
        $out[] = [
            'url'  => $url,
            'icon' => safe_filename((string)($r['icon'] ?? 'globe.svg')),
            'type' => trim((string)($r['type'] ?? '')),
        ];
    }
    return $out;
}

function link_row(string $prefix, array $l): void
{
    ?>
    <div class="repeater__row">
        <input type="url" name="<?= h($prefix) ?>[url]" value="<?= h($l['url'] ?? '') ?>" placeholder="https://…">
        <input name="<?= h($prefix) ?>[type]" value="<?= h($l['type'] ?? '') ?>" placeholder="label (github, website…)">
        <?= icon_select($prefix . '[icon]', (string)($l['icon'] ?? '')) ?>
        <button type="button" class="btn btn--ghost repeater__remove" title="Remove link">✕</button>
    </div>
    <?php
}

function project_form(?int $i, array $p): void
{
    $t = $p['title'] ?? [];
    ?>
    <form method="post" class="form-grid">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="index" value="<?= $i ?? '' ?>">

        <?php foreach (LANGS as $code => $name): ?>
            <label><?= h($name) ?> title <?= $code === 'en' ? '(required)' : '(optional)' ?>
                <input name="title[<?= $code ?>]" value="<?= h($t[$code] ?? '') ?>">
            </label>
        <?php endforeach; ?>

        <h3>Links</h3>
        <div class="repeater">
            <div class="repeater__rows">
                <?php foreach ($p['links'] ?? [] as $n => $l) link_row("links[$n]", $l); ?>
            </div>
            <template><?php link_row('links[__i__]', []); ?></template>
            <button type="button" class="btn btn--ghost repeater__add">+ Add link</button>
        </div>

        <button class="btn">Save project</button>
    </form>
    <?php
}

/* ---------- POST handling ---------- */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data     = json_load('projects.json');
        $projects = $data['projects'];
        $action   = $_POST['action'] ?? '';
        $i        = ($_POST['index'] ?? '') === '' ? null : (int)$_POST['index'];

        if ($action === 'save') {
            $title = clean_multilang($_POST['title'] ?? []);
            if ($title['en'] === '') {
                throw new RuntimeException('English title is required.');
            }
            $project = ['title' => $title, 'links' => clean_links($_POST['links'] ?? [])];

            if ($i === null) {
                $projects[] = $project;
                flash('Project added.');
            } elseif (isset($projects[$i])) {
                $projects[$i] = $project;
                flash('Project saved.');
            } else {
                throw new RuntimeException('Project not found.');
            }
        } elseif ($action === 'delete' && $i !== null && isset($projects[$i])) {
            array_splice($projects, $i, 1);
            flash('Project deleted.');
        } elseif ($action === 'move' && $i !== null && isset($projects[$i])) {
            $j = ($_POST['dir'] ?? '') === 'up' ? $i - 1 : $i + 1;
            if (isset($projects[$j])) {
                [$projects[$i], $projects[$j]] = [$projects[$j], $projects[$i]];
            }
        }

        $data['projects'] = array_values($projects);
        json_save('projects.json', $data);
    } catch (Throwable $e) {
        flash($e->getMessage(), 'err');
    }
    redirect('projects.php');
}

/* ---------- render ---------- */

$projects = json_load('projects.json')['projects'];
admin_header('Projects');
?>
<h1>Projects</h1>
<p class="muted">Shown on <code>projects.php</code>. Titles fall back to English when a translation is empty.</p>

<details class="card">
    <summary>+ New project</summary>
    <?php project_form(null, []); ?>
</details>

<?php foreach ($projects as $i => $p): ?>
    <details class="card">
        <summary>
            <?= h($p['title']['en'] ?? '(untitled)') ?>
            <span class="muted"><?= count($p['links'] ?? []) ?> links</span>
        </summary>
        <?php project_form($i, $p); ?>
        <div class="row-actions">
            <?= action_form('move', $i, '↑ Move up', ['dir' => 'up']) ?>
            <?= action_form('move', $i, '↓ Move down', ['dir' => 'down']) ?>
            <?= action_form('delete', $i, 'Delete project', [], 'btn btn--danger', 'Delete this project?') ?>
        </div>
    </details>
<?php endforeach; ?>
<?php admin_footer();