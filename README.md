# LittleOne PHP: tiny php framework
## Setup
1. Rewrite current location to your `index.php?spine-location=<current location>` that is your app's starting point. Nginx example: `rewrite ^(.+)$ /index.php?spine-location=$1 break;`
2. Require the `littleone.php` in your `index.php` file.
3. Add some routes and run it! That's it!

  If you want to play with it, run `docker-compose up` and hit `http://localhost:1111`. It uses the app from `usage-examples` directory.

## Usage examples
```php
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
```

## TODO
- add possibility to add Post, Put and Delete routes

## Licence
MIT