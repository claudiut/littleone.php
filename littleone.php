<?php

namespace LittleOne;

class LittleOne
{
  private $layout;

  private $routes = [];

  public function __construct(array $config = [])
  {
    if (isset($config['layout']) && is_string($config['layout'])) {
      $this->layout = $config['layout'];
    }
  }

  public function addRoute(string $route, ...$renderMethods)
  {
    $route = empty($route) ? '/' : $route;

    if (isset($this->routes[$route])) {
      $this->routes[$route] = array_merge($this->routes[$route], $renderMethods);
    } else {
      $this->routes[$route] = $renderMethods;
    }
  }

  public function get(...$args)
  {
    $this->addRoute(...$args);
  }

  public function run()
  {
    $uri = empty($_GET['spine-location']) || trim(htmlspecialchars($_GET['spine-location']), '/') === 'index.php'
      ? '/'
      : $this->normalizeUriPath(htmlspecialchars($_GET['spine-location']));

    foreach ($this->routes as $path => $controlMethods) {
      // $path = $this->normalizeUriPath($path);
      $params = $path === $uri ? [] : $this->params($path, $uri);

      if ($path === $uri || !empty($params))
        foreach ($controlMethods as $method)
          call_user_func_array($method, $params);
    }
  }

  /**
   * @param string $fileOrContents file to render
   * @param array $locals local variables to use inside the rendered file
   * @param array $options options such as using or not using a layout when rendering
   */
  public function render(string $fileOrContents, array $locals = [], array $options = []): void
  {
    $options['layout'] = isset($options['layout']) ? $options['layout'] : true;
    $options['file']   = isset($options['file']) ? $options['file'] : true;

    // check file type to tell the browser to render html or some other file type
    $isPhpFile = false;
    if ($options['file']) {
      $parts = explode('.', basename($fileOrContents));
      $isPhpFile = count($parts) > 1 && array_pop($parts) === 'php';
    }

    $options['type'] = $options['file']
      ? ($isPhpFile ? 'text/html' : mime_content_type($fileOrContents))
      : finfo_buffer(finfo_open(), $fileOrContents, FILEINFO_MIME_TYPE);

    // this is used in both 'yield' method and in the layout php file
    $locals = $locals && is_array($locals) ? $locals : [];

    $yield = function () use ($fileOrContents, $isPhpFile, $locals, $options) {
      // if contents, echo them
      if (!$options['file']) {
        echo $fileOrContents;
        return;
      }

      // if we render a file, for php require it, or else just read/serve it
      if ($isPhpFile)
        require($fileOrContents);
      else
        readfile($fileOrContents);
    };

    header('Content-Type: ' . $options['type']);
    header('Content-Disposition: inline');

    if ($isPhpFile && $options['layout']) {
      // the layout will call the $yield function
      require($this->layout);
      return;
    }

    $yield();
  }

  // HELPER methods
  public function params($route, $uri)
  {
    $route = $this->normalizeUriPath($route);
    $uri  = $this->normalizeUriPath($uri);

    // check if route has any params (like :paramX)
    $paramRegexp = '/\:[a-zA-Z0-9]+/';

    preg_match($paramRegexp, $route, $m);

    if (empty($m))
      return [];

    // if route has params, create a regexp from it
    // and match the request path with that regexp to extract the param values from the route
    $routeRegexp = preg_replace($paramRegexp, '(.+)', $route);
    $routeRegexp = str_replace('/', '\/', $routeRegexp);

    preg_match('/' . $routeRegexp . '/', $uri, $params);
    array_shift($params);

    return $params;
  }

  public function normalizeUriPath($uri)
  {
    return '/' . trim($uri, '/');
  }
}
