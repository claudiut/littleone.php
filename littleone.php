<?php

class LittleOne {
  private static $routes = [];
  private static $layout;

  public static function route($path) {
    $path = empty($path) ? '/' : $path;
    $args = func_get_args();
    array_shift($args);

    if(isset(self::$routes[$path]))
      self::$routes[$path] = array_merge(self::$routes[$path], $args);
    else
      self::$routes[$path] = $args;
  }

  public static function start() {
    $uri = empty($_GET['spine-location']) || trim(htmlspecialchars($_GET['spine-location']), '/') === 'index.php'
            ?
          '/'
            :
          self::normalizeUriPath(htmlspecialchars($_GET['spine-location']));

    foreach(self::$routes as $path => $controlMethods) {
      $path = self::normalizeUriPath($path);

      $params = $path === $uri ? [] : self::params($path, $uri);

      if($path === $uri || !empty($params))
        foreach($controlMethods as $method)
          call_user_func_array($method, $params);
    }
  }

  // VIEW
  public static function layout($layoutFilePath) {
    self::$layout = $layoutFilePath;
  }

  public static function render($fileOrContents, $options=[]) {
    $options['layout'] = isset($options['layout']) ? $options['layout'] : true;
    $options['type']   = isset($options['type']) ? $options['type'] : 'text/html';

    $yield = function() use ($fileOrContents, $options) {
      if(isset($options['input']) && $options['input'] === 'contents')
        echo $fileOrContents;
      else {
        $parts = explode('.', $fileOrContents);
        if(isset($parts[1]) && $parts === 'php')
          require($fileOrContents);
        else
          readfile($fileOrContents);
      }
    };

    header('Content-Type: ' . $options['type']);
    header('Content-Disposition: inline');

    if($options['layout'])
      require(self::$layout);
    else
      $yield();
  }

  // HELPER methods
  public static function params($path, $uri) {
    $path = self::normalizeUriPath($path);
    $uri  = self::normalizeUriPath($uri);

    $paramRegexp = "/\:[a-zA-Z0-9]+/";

    preg_match($paramRegexp, $path, $m);

    if(empty($m))
      return [];

    $pathRegexp = preg_replace($paramRegexp, "(.+)", $path);
    $pathRegexp = str_replace("/", "\/", $pathRegexp);

    preg_match("/" . $pathRegexp . "/", $uri, $params);
    array_shift($params);

    return $params;
  }

  public static function normalizeUriPath($uri) {
    return '/' . trim($uri, '/');
  }
}