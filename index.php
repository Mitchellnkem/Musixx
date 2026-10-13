<?php require_once('./config.php'); ?>
<!DOCTYPE html>
<html lang="en">
<?php require_once('inc/header.php') ?>
  <body class="site-body" data-page="<?= e(isset($_GET['page']) && is_string($_GET['page']) ? $_GET['page'] : 'home') ?>">
    <div class="site-shell">
     <?php require_once('inc/topBarNav.php') ?>
     <?php if($_settings->chk_flashdata('success')): ?>
      <div class="site-notice" role="status"><i class="fas fa-check-circle" aria-hidden="true"></i> <?= e($_settings->flashdata('success')) ?></div>
     <?php endif;?>
     <?php
        // Only route to known application pages; never use a request value as a filesystem path.
        $routes = [
          'home' => 'home.php',
          'categories' => 'categories.php',
          'music_list' => 'music_list.php',
          'about' => 'about.php',
          'music_view' => 'view_music.php',
          'favorites' => 'favorites.php',
        ];
        $page = isset($_GET['page']) && is_string($_GET['page']) ? $_GET['page'] : 'home';
     ?>
      <main class="site-main" id="main-content">
        <div class="site-container">
            <?php 
              if(!isset($routes[$page])){
                  include '404.html';
              }else{
                include $routes[$page];
              }
            ?>
        </div>
      </main>
      <?php require_once('inc/footer.php') ?>
    </div>
 </body>
</html>
