<?php
require_once '../src/languages.php';
// Ensure the session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getCurrentLang() {
  if (isset($_GET['lang'])) {
      return $_GET['lang'];
  }
  
  if (isset($_COOKIE['preferred_lang'])) {
      return $_COOKIE['preferred_lang'];
  }
  
}

$currentLang = getCurrentLang();
$_SESSION['lang'] = $currentLang;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Driving Blog</title>
  <link rel="stylesheet" href="/Webster/style.css">
</head>
<body>
  <div class="container">
    <header class="header">
      <a href="../index.php"><h3>Hello World</h3></a>
      <nav class="menu">
        <ul class="menu__inner">
          <li>
            <a href="../about.php"><?php echo t('About'); ?></a>
          </li>
          <li>
            <a href="../projects.php"><?php echo t('Projects'); ?></a>
          </li>
          <li>
            <a href="../blogs.php"><?php echo t('Blogs'); ?></a>
          </li>
        </ul>
      </nav>
    </header>
    <main class="post">
      <article class="blog__article">
      <?php
      require_once ('Parsedown.php');

      $Parsedown = new Parsedown();
      $myblog = fopen("markdowns/blog.md", "r") or die("Unable to open file!");
      echo  $Parsedown->text(fread($myblog, filesize("markdowns/blog.md")));
      fclose($myblog);
      ?>
      </article>
      <footer>
        <br>
        <p>11-3-2024</p>
        <p>Afbeelding credits naar: Rijschool animo</p>
      </footer>
    </main>
  </div>
</body>
</html>
