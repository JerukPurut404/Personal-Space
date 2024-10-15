<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portofolio</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="container">
    <header class="header">
      <a href="index.html">
        <div class="logo">
          <h3 class="logo__symbol">Hello world</h3>
        </div>
      </a>
      <nav class="menu">
        <ul class="menu__inner">
          <li>
            <a href="/about.html">About</a>
          </li>
          <li>
            <a href="/projects.html">Projects</a>
          </li>
          <li>
            <a href="/articles.html">Articles</a>
          </li>
        </ul>
      </nav>
    </header>
      <main class="post">
        <h1>These are SD webrings:</h1>
        <?php  
        $json = file_get_contents('webring.json');
        $webring = json_decode($json, true);
        if (isset($_GET['action']) && $_GET['action'] === 'random') {
            $randomIndex = array_rand($webring);
            header("Location: ". $webring[$randomIndex]['url']);
            exit;
        }
        if (!isset($_GET['action']) || $_GET['action']!== 'random') {
            foreach ($webring as $website) {
                echo '<a href="'. $website['url']. '">'. $website['title']. '</a><br>';
            }
        }?>
      </main>
      <footer class="footer">
        <div class="footer__inner">
          <div class="footer__content">
            <a href="https://polarisfm.neocities.org/">Inspired by Polarisfm</a>
            <p>|</p>
            <p>Made by E. van Aubel</p>
          </div>
        </div>
      </footer> 
    </div>
</body>

</html>



