<?php
define('CMS_PUBLIC', true);
require __DIR__ . '/includes/lib.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (login((string)($_POST['password'] ?? ''))) {
        redirect('index.php');
    }
    sleep(1);   // slow down brute force attempts
    $error = 'Wrong password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title>CMS login</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
<main class="card login">
  <h1>CMS login</h1>
  <?php if ($error): ?><div class="flash flash--err"><?= h($error) ?></div><?php endif; ?>
  <form method="post" class="form-grid">
    <?= csrf_field() ?>
    <label>Password <input type="password" name="password" autofocus required></label>
    <button class="btn">Log in</button>
  </form>
</main>
</body>
</html>