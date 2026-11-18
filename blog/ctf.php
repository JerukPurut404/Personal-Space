<?php
require_once '../src/languages.php';

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
  <title>Blog</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
  <div class="container">
    <header class="header">
      <a href="../index.php">
        <div class="logo">
          <h3 class="logo__symbol">Hello world</h3>
        </div>
      </a>
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
    <div class="content">
      <main class="post">
        <?php
        require_once ('Parsedown.php');

        $Parsedown = new Parsedown();
        $myblog = fopen("markdowns/ctf.md", "r") or die("Unable to open file!");
        echo  $Parsedown->text(fread($myblog, filesize("markdowns/ctf.md")));
        fclose($myblog);
        ?>        
        <hr>
        <div class="post__info">
          <?php
          $blogsData = json_decode(file_get_contents(__DIR__ . '/../json/blogs.json'), true);
          $slug = basename($_SERVER['SCRIPT_NAME']);
          $postDate = '';
          foreach ($blogsData['blogs']['items'] ?? [] as $item) {
              if (basename($item['href'] ?? '') === $slug) {
                  $postDate = $item['date'] ?? '';
                  break;
              }
          }
          echo '<p>' . htmlspecialchars($postDate) . '</p>';
          ?>
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