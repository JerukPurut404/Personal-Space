<?php
require_once 'src/languages.php';

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
  
  return 'en';
} 

$currentLang = getCurrentLang();
$_SESSION['lang'] = $currentLang;
?>
<!DOCTYPE html>
<html lang="<?php echo $currentLang; ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo t('Projects'); ?></title>
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
          <li><a href="about.php"><?php echo t('About'); ?></a></li>
          <li><a href="projects.php"><?php echo t('Projects'); ?></a></li>
          <li><a href="blogs.php"><?php echo t('Blogs'); ?></a></li>
        </ul>
      </nav>
    </header>
    <div class="content">
      <main class="post">
        <article class="post__article">
          <h2 class="post__title"><?php echo t('Projects'); ?></h2>
          <div class="post__content">
            <p><?php echo t("These are my previous projects in my school and all of them are avaliable in Github");?></p>
            <?php
            $json = file_get_contents('json/projects.json');

            if ($json === false) {
                die('Error reading the JSON file');
            }

            $json_data = json_decode($json, true); 
                
            if ($json_data === null) {
                die('Error decoding the JSON file');
            }

            foreach ($json_data['projects'] as $project) {
                echo "<ul class='post__lists'>";
                echo "<section class='post__icons'>";
                foreach ($project['links'] as $link) {
                  echo "<li class='post__list'> <a href='" . htmlspecialchars($link['url']) . "'><img class='icon__svg' src='media/" . htmlspecialchars($link['icon']) . "' alt='" . htmlspecialchars($link['type']) . " icon'></a></li>";
                }
                echo "</section>";
                
                // Check if the translation exists, fall back to English if not
                $title = isset($project['title'][$currentLang]) ? $project['title'][$currentLang] : $project['title']['en'];
                echo "<li class='post__list'>" . htmlspecialchars($title) . "</li>";
                echo "</ul>";
            }
            ?>
          </div>
        </article>
      </main>
      <footer class="footer">
        <div class="footer__inner">
          <div class="footer__content">
            <p><?php echo t("Made by E. van Aubel"); ?></p>
          </div>
        </div>
      </footer>
    </div>
  </body>
</html>
