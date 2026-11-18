<?php
require __DIR__ . '/includes/lib.php';
require_once ROOT_DIR . '/blog/Parsedown.php';

echo (new Parsedown())->text((string)($_POST['markdown'] ?? ''));