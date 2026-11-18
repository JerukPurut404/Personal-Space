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

$galleryPath = __DIR__ . '/json/gallery.json';
$gallery = file_exists($galleryPath)
    ? json_decode(file_get_contents($galleryPath), true)
    : ['items' => []];

$items = $gallery['items'] ?? [];

// Sort newest first
usort($items, fn($a, $b) => strcmp($b['date'] ?? '', $a['date'] ?? ''));
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($currentLang, ENT_QUOTES, 'UTF-8'); ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo t('Gallery'); ?></title>
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
          <h2 class="post__title"><?php echo t('Gallery'); ?></h2>
          <p><?php echo t('A collection of photos.'); ?></p>

          <?php if (empty($items)): ?>
            <p><em><?php echo t('No photos yet.'); ?></em></p>
          <?php else: ?>
            <div class="gallery__grid">
              <?php foreach ($items as $item):
                $title = htmlspecialchars($item['title'][$currentLang] ?? $item['title']['en'] ?? '', ENT_QUOTES, 'UTF-8');
                $desc  = htmlspecialchars($item['description'][$currentLang] ?? $item['description']['en'] ?? '', ENT_QUOTES, 'UTF-8');
                $date  = htmlspecialchars($item['date'] ?? '', ENT_QUOTES, 'UTF-8');
                $src   = htmlspecialchars($item['src'] ?? '', ENT_QUOTES, 'UTF-8');
              ?>
                <figure class="gallery__item">
                  <a href="<?php echo $src; ?>" target="_blank" rel="noopener">
                    <img src="<?php echo $src; ?>" alt="<?php echo $title; ?>" loading="lazy">
                  </a>
                  <figcaption class="gallery__caption">
                    <h3 class="gallery__title"><?php echo $title; ?></h3>
                    <p class="gallery__date"><?php echo $date; ?></p>
                    <?php if ($desc !== ''): ?>
                      <p class="gallery__desc"><?php echo $desc; ?></p>
                    <?php endif; ?>
                  </figcaption>
                </figure>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </article>
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