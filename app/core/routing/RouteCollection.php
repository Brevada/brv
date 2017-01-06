<?php
/**
 * RouteCollection
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\core\routing;

use Brv\core\routing\Route;

/**
 * Represents a collection of routes.
 */
class RouteCollection implements \Iterator
{
    /** @var array The router configuration from which the RouteCollection is based. */
    private $config;

    /** @var array An array of middleware aliases. */
    private $middleware;

    /** @var Route[] An array of Route objects. */
    private $routes;

    /** @var integer Current position in the routes array. */
    private $position;

    /**
     * Instantiates a new RouteCollection from a configuration file path.
     *
     * @param string $path A YAML configuration file from which to construct the RouteCollection.
     */
    public function __construct($path)
    {
        $this->config = $this->loadConfig($path);
        $this->middleware = $this->extractMiddleware();
        $this->routes = $this->extractRoutes();

        $this->rewind();
    }

    /**
     * Rewinds the iterator.
     */
    public function rewind()
    {
        // Reset to beginning.
        $this->position = -1;
        $this->next();
    }

    /**
     * Returns the current route.
     *
     * @return Route
     */
    public function current()
    {
        $this->routes[$this->position]->expandMiddleware($this->middleware);
        $this->routes[$this->position]->expandController();

        // Get current value.
        return $this->routes[$this->position];
    }

    /**
     * Returns the current route name.
     *
     * @return string
     */
    public function key()
    {
        // Get current key
        return $this->routes[$this->position]->getName();
    }

    /**
     * Advances the iterator to the next valid route.
     */
    public function next()
    {
        // Advance to next position.
        do {
            $this->position++;
        } while ($this->valid() && !$this->routes[$this->position]->isValid());
    }

    /**
     * Returns the validity of the current route.
     * @return boolean
     */
    public function valid()
    {
        // If current position is valid.
        return $this->position < count($this->routes);
    }

    /**
     * Loads the router configuration file from the default location.
     *
     * @throws \Exception on invalid route.
     * @param string $path The file path of the configuration file.
     * @return array The loaded configuration in an associated KV-pair array.
     */
    private function loadConfig($path)
    {
        if (file_exists($path)) {
            $config = yaml_parse_file($path);
            if ($config === false) {
                \App::log()->error("Unable to parse yaml router configuration file at: ". $path);
                throw new \Exception("Invalid routes.");
            }
            return $config;
        }

        \App::log()->error("Router configuration file does not exist: " . $path);
        throw new \Exception("No routes defined.");
    }

    /**
     * Extracts Middleware metadata from router configuration.
     *
     * This allows aliases to be setup in the configuration file.
     *
     * @return array An array of middleware alias metadata.
     */
    private function extractMiddleware()
    {
        /* Create default "pass" alias which allows "null" middleware. */
        $middleware = [ 'pass' => true ];
        if (isset($this->config['middleware'])) {
            $middleware = array_merge($middleware, $this->config['middleware']);
        }
        return $middleware;
    }

    /**
     * Extracts routes from the router configuration.
     *
     * @return Route[] An array of Routes.
     */
    private function extractRoutes()
    {
        $rawRoutes = isset($this->config['routes']) ? $this->config['routes'] : [];

        $routes = [];

        /* Support embedding method in route pattern, as well as explicitly
         * defining the method in a KV format. */
        foreach ($rawRoutes as $name => $routeRaw) {
            if (!self::isMethodDelimited($routeRaw)) {
                /* Method is embedded in pattern, extract it and "correct" pattern. */
                $method = trim(substr($routeRaw[0], 0, strpos($routeRaw[0], ' ')));
                $routeRaw[0] = trim(substr($routeRaw[0], strlen($method)));
                $routeRaw = [
                    $method => $routeRaw
                ];
            }

            foreach ($routeRaw as $method => $route) {
                $routes[] = new Route([
                    "name" => $name,
                    "method" => $method,
                    "pattern" => $route[0],
                    "middleware" => $route[1],
                    "controller" => $route[2]
                ]);
            }
        }

        return $routes;
    }

    /**
     * Determines if a raw route is already delimited by methods.
     *
     * @param array $route The raw route to check.
     * @return boolean
     */
    public static function isMethodDelimited($route)
    {
        return count(array_filter(array_keys($route), 'is_string')) > 0;
    }
}
