<?php
require_once 'src/languages.php';

// Ensure the session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getCurrentLang() {
    $allowed = ['en', 'nl', 'id'];
    $lang = $_GET['lang'] ?? $_COOKIE['preferred_lang'] ?? 'en';
    return in_array($lang, $allowed, true) ? $lang : 'en';
}

$currentLang = getCurrentLang();
$_SESSION['lang'] = $currentLang;
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($currentLang, ENT_QUOTES, 'UTF-8'); ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Articles</title>
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
          <li><a href="gallery.php"><?php echo t('Gallery'); ?></a></li>
        </ul>
      </nav>
    </header>
    <div class="content">
      <main class="post">
        <article class="post__article">
          <h2 class="post__title"><?php echo t('Blogs')?></h2>
          <div class="post__content">
            <p><?php echo  t("These are my own articles/blog mostly about my own projects, rant's, thought's etc")?></p>
            <?php
            // Load JSON file
            $json = file_get_contents('json/blogs.json');

            if ($json === false) {
                die('Error reading the JSON file');
            }

            $json_data = json_decode($json, true); 

            if ($json_data === null) {
                die('Error decoding the JSON file');
            }

            // Check for language parameter
            $lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';
            // Generate HTML output based on selected language
            $html = "<ul class='post__lists'>";
            foreach ($json_data['blogs']['items'] as $blogs) {
                $text = $blogs['text'][$lang] ?? $blogs['text']['en'];
                $sub  = $blogs['subtext'][$lang] ?? $blogs['subtext']['en'];
                $html .= "<li class='" . htmlspecialchars($blogs['class']) . "'>";
                $html .= "<a href='" . htmlspecialchars($blogs['href']) . "'>" . htmlspecialchars($text) . "</a>";
                $html .= "<span class='post__subtext'>" . htmlspecialchars($sub) . "</span>";
                $html .= "<time class='post__date' datetime='" . htmlspecialchars($blogs['date']) . "'>" . date('Y-m-d', strtotime($blogs['date'])) . "</time>";
                $html .= "</li>";
            }
            $html .= "</ul>";

            echo $html;
            ?>
          </div>
        </article>
      </main>
    </div>
    <footer class="footer">
      <div class="footer__inner">
        <div class="footer__content">
          <p><?php echo t('Made by E. van Aubel')?></p>
        </div>
      </div>
    </footer>
  </div>
</body>
</html>