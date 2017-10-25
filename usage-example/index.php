<?php

// NOTE: you have to rewrite the urls to "index.php?spine-location=$1" as in my .htaccess example

include_once "../littleone.php";

LittleOne::route("/", function() {
  echo "raw index page part1..<br>";
},
function() {
  echo "raw index page part2..<br>";
});

LittleOne::route("/", function() {
  echo "raw index page part3..";
});



LittleOne::route("/profile", function() {
  LittleOne::render('./my-views/profile.php');
});

LittleOne::route("/settings", function() {
  LittleOne::render('./my-views/settings.php');
});



LittleOne::route("/page-without-layout", function() {
  LittleOne::render('./my-views/profile.php', ['layout' => false]);
});



LittleOne::route("/image", function() {
  $imgFile = './assets/images/pexels-photo-619948.jpeg';
  LittleOne::render($imgFile, ['type' => mime_content_type($imgFile), 'layout' => false]);
});

LittleOne::route("/json-content", function() {
  LittleOne::render(json_encode(['key' => 'value']), ['type' => 'application/json', 'input' => 'contents', 'layout' => false]);
});



LittleOne::route("/cars/:carId/parts/:partId", function($carId, $partId) {
  echo "View part {$partId} of the car {$carId}";
});


LittleOne::layout('./my-views/layout.php');
LittleOne::start();