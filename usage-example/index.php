<?php

include_once '../littleone.php';

use \LittleOne\LittleOne;

$app = new LittleOne(['layout' => 'views/layout.php']);

$app->get('/', function () {
  echo 'Some raw content';
});

$app->get('/pages/:pageId', function ($pageId) use ($app) {
  $app->render('views/page.php', ['pageId' => $pageId]);
});

// chain render methods
$app->get(
  '/composed',
  function () {
    echo 'Public content';
  },

  function () {
    if ($_SESSION['authenticated'] ?: false) {
      echo '<br>User content';
    }
  },

  function () {
    echo '<br>More public content.';
  }
);

// render view without the layout
$app->get('/no-layout', function () use ($app) {
  $app->render('views/profile.php', null, ['layout' => false]);
});

// render image file
$app->get('/image', function () use ($app) {
  $app->render('./assets/images/img.jpeg');
});

$app->run();
