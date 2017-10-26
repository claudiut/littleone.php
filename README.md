# LittleOne PHP MVC Framwork (In Progress)
Just require the littleone.php and use the .htaccess file as in the usage-example/index.php and start creating your app!

# Usage
```php
// NOTE: you have to rewrite the urls to "index.php?spine-location=$1" as in my .htaccess example
include_once '../littleone.php';

LittleOne::route('/', function() {
  echo 'Raw index page';
});

LittleOne::route('/profile', function() {
  LittleOne::render('views/profile.php');
});

LittleOne::layout('views/layout.php');
LittleOne::start();
```

## Extended usage examples
```php
include_once '../littleone.php';

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

LittleOne::route('/pages/:pageId', function($pageId) {
  LittleOne::render('views/page.php', ['pageId' => $pageId]);
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
```

# TODO
- the ability to decouple the Controller methods in separate controller files
- the Model
- 404 Not Found page
- eventually, the ability to specify the views and assets paths

# Credits
Inspired from ExpressJS and Laravel.