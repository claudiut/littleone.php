<?php

// NOTE: you have to rewrite the urls to "index.php?spine-location=$1" as in my .htaccess example

include_once '../littleone.php';

LittleOne::route('/', function() {
  echo 'Raw index page';
});

LittleOne::route('/pages/:pageId', function($pageId) {
  LittleOne::render('views/page.php', ['pageId' => $pageId]);
});

// chain render methods
LittleOne::route('/composed-and-filtered',
  function() {
      echo 'Part1<br>';
  },

  function() {
    echo 'Part2';
    
    if(empty($_SESSION['authenticated']))
      die();
  },
  
  function() {
      echo 'Part3 - only for authenticated users.';
  }
);

// get route params
LittleOne::route('/cars/:carId/parts/:partId', function($carId, $partId) {
  echo "View part {$partId} of the car {$carId}";
});


// render views inside a layout
LittleOne::route('/profile', function() {
  LittleOne::render('views/profile.php');
});

LittleOne::route('/settings', function() {
  LittleOne::render('views/settings.php');
});


// render view without the layout
LittleOne::route('/page-without-layout', function() {
  LittleOne::render('views/profile.php', null, ['layout' => false]);
});


// render image file
LittleOne::route('/render-image', function() {
  LittleOne::render('./assets/images/pexels-photo-619948.jpeg');
});

// render some content
LittleOne::route('/json-content', function() {
  LittleOne::render(json_encode(['key' => 'value']), null, ['file' => false]);
});


LittleOne::layout('views/layout.php');
LittleOne::start();