<?php
require_once 'src/languages.php';

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

// Load about data
$aboutPath = __DIR__ . '/json/about.json';
$about = file_exists($aboutPath)
    ? json_decode(file_get_contents($aboutPath), true)
    : null;

$profilePic = htmlspecialchars($about['profile_pic'] ?? '', ENT_QUOTES, 'UTF-8');
$profileAlt = htmlspecialchars($about['profile_alt'] ?? 'Profile Picture', ENT_QUOTES, 'UTF-8');
$contentHtml = $about['content_html'][$currentLang]
    ?? $about['content_html']['en']
    ?? '<p>About page not configured.</p>';
$aboutDate = htmlspecialchars($about['date'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($currentLang, ENT_QUOTES, 'UTF-8'); ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo t('About'); ?></title>
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
          <li><a href="gallery.php"><?php echo t('Gallery'); ?></a></li>
        </ul>
      </nav>
    </header>
    <div class="content">
      <main class="post">
        <article class="post__article">
          <h2 class="post__title"><?php echo t('About me'); ?></h2>
          <div class="about__content">
            <div class="about__pic">
              <img src="<?php echo $profilePic; ?>" alt="<?php echo $profileAlt; ?>">
            </div>
            <?php echo $contentHtml; ?>
          </div>
        </article>
        <hr>
        <div class="post__info">
          <p><?php echo $aboutDate; ?></p>
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