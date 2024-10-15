<?php
require_once 'src/languages.php';

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
  <title>About Me</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <header class="header">
      <a href="index.php">
        <div class="logo">
          <h3 class="logo__symbol">Hello world</h3>
        </div>
      </a>
      <nav class="menu">
        <ul class="menu__inner">
          <li>
            <a href="about.php"><?php echo t('About')?></a>
          </li>
          <li>
            <a href="projects.php"><?php echo t('Projects')?></a>
          </li>
          <li>
            <a href="blogs.php"><?php echo t('Blogs')?></a>
          </li>
        </ul>
      </nav>
    </header>
    <div class="content">
      <main class="post">
        <article class="post__article">
          <h2 class="post__title"><?php echo t('About me')?></h2>
          <div class="post__content">
            <picture>
              
            </picture>
            <p><?php echo t("E. Van Aubel is a software development student who is scared of CSS and is living in Amsterdam, Netherlands. They are passionate about free and open-source software and privacy advocacy. Their skills include programming in Python and JavaScript, web development with CSS and HTML, and proficiency in the Laravel framework and Tailwind CSS. E. Van Aubel enjoys movies, anime, and music in their free time. They are equipped with an HP 15s-eq0xxx laptop featuring an AMD Ryzen 3 3200U processor. This young developer is well-positioned to contribute to innovative tech projects with their growing expertise in software development and commitment to open-source principles.")?></p>
          </div>
        </article>
        <hr>
        <div class="post__info">
          <p>11-3-2024</p>
        </div>
      </main>
    </div>
    <footer class="footer">
      <div class="footer__inner">
        <div class="footer__content">
          <p>Made by E. van Aubel</p>
        </div>
      </div>
    </footer>
  </div>
</body>
</html>