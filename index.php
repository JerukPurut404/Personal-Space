<?php
require_once 'src/languages.php';

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

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    setcookie('preferred_lang', $lang, time() + 60*60*24*30);
    header("Location: index.php?lang=$lang");
        exit;
}
?>

<!DOCTYPE html>
<html lang="<?php echo isset($_GET['lang']) ? $_GET['lang'] : 'en'; ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portofolio</title>
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
      <main class="content__main">
        <div class="content__div">
          <img class="profile__picture" src="media/image_proxy.png">
          <img class="handwriting__svg" src=<?php echo t('media/handwriting/welcome.svg')?> alt="">
          <br>
          <img class="handwriting__svg" src=<?php echo t('media/handwriting/im-e-van-aubel.svg')?> alt="">
          <br>
          <img class="handwriting__svg" src=<?php echo t('media/handwriting/im-a-software-developer.svg')?> alt="">
          <br>
        </div>
            <?php
            $json = file_get_contents('json/socials.json');

            if ($json === false) {
              die('Error reading the JSON file');
            }

            $json_data = json_decode($json, true); 
            
            if ($json_data === null) {
              die('Error decoding the JSON file');
            }
            $html = '<div class="content__menu">';
            foreach ($json_data['socials']['links'] as $link) {
              $html .= '<a href="' . htmlspecialchars($link['href']) . '">';
              $html .= '<img class="icon__svg" src="' . htmlspecialchars($link['iconSrc']) . '" alt="' . htmlspecialchars($link['iconAlt']) . '"/>';
              $html .= '</a>';
            }

            $html .= '</ul>';
            $html .= '</div>';

            echo $html;
            ?>
      </main>
      <main class="post">
        <article class="post__article">
            <ul class="post__lists">
                <li class="post__list"><?php echo t('Highlighted Skills')?></li>
                <a href="https://www.python.org/"><img class="icon__svg" src="media/python.svg" alt="python icon"></a>
                <a href="https://www.php.net/"><img class="icon__svg" src="media/php.svg" alt="php icon"></a>
                <a href="https://developer.mozilla.org/en-US/docs/Web/javascript"><img class="icon__svg" src="media/javascript.svg" alt="javascript icon"></a>
                <a href="https://developer.mozilla.org/en-US/docs/Web/CSS"><img class="icon__svg" src="media/css3.svg" alt="css icon"></a>
            </ul>
            <ul class="post__lists">
              <li class="post__list"><?php echo t('Highlighted Project')?></li>
              <section class="post__icons">
                <li class="post__list"> <a href="https://github.com/JerukPurut404/EindOpdrachtVGM"><img class="icon__svg" src="media/github.svg" alt="github icon"></a> </li>
                <li class="post__list"> <a href="https://35023.hosts1.ma-cloud.nl/EindOpdrachtVGM/Eugene's_folder/index.html"><img class="icon__svg" src="media/globe.svg" alt="website icon"></a></li> 
              </section>
              <li class="post__list"><?php echo t('National Videogame Museum Redesign Website.')?></li>
            </ul>
            <li class="box-wrapper">
              <ul class="box__lists">
                <li class="box__list"><?php echo t('View my CV here:')?></li>
                <section class="box__icons">
                  <a href="https://rxresu.me/john.krow/e-van-aubel" class="cv__button"><?php echo t('Go to my CV')?></a>
                </section>
              </ul>
              <ul class="box__lists">
                <li class="box__list"><?php echo t('Select Language:')?></li>
                <section class="box__icons">
                    <li class="box__list">
                        <a href="index.php?lang=nl" class="language__button">NL</a>
                    </li>
                    <li class="box__list">
                        <a href="index.php?lang=en" class="language__button">EN</a>
                    </li>
                    <li class="box__list">
                        <a href="index.php?lang=id" class="language__button">ID</a>
                    </li>
                </section>
            </ul>
          </li>
        </article>
      </main>
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