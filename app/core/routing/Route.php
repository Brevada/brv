<?php
/**
 * Route
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\core\routing;

/**
 * Represents a single route.
 */
class Route
{
    /** @var string The name of the route. Used in debugging and logs. */
    private $name;

    /** @var string The HTTP method type. */
    private $method;

    /** @var string|string[] The (optionally chained) middleware class names. */
    private $middleware;

    /** @var string The RegEx pattern to match the URL with. */
    private $pattern;

    /** @var string The controller class name, or engine name. */
    private $controller;

    /** @var string|boolean The controller method name or false to indicate generic execute. */
    private $controllerArg;

    /** @var array|boolean The matches from applying the pattern to the URL. */
    private $matches = false;

    /** @var boolean Indicates whether the middleware has been expanded (aliases resolved). */
    private $expandedMiddleware = false;

    /**
     * Indicates whether the controller information has been
     * extracted out of the controller string.
     * @var boolean
     */
    private $expandedController = false;

    /**
     * Instantiates a new Route.
     *
     * @param array $opts The required Route configuration options.
     */
    public function __construct(array $opts)
    {
        $this->name = $opts['name'];
        $this->pattern = $opts['pattern'];
        $this->method = $opts['method'];
        $this->middleware = $opts['middleware'];
        $this->controller = $opts['controller'];
    }

    /**
     * Gets the route name.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the route method.
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Gets the route URL pattern matches.
     * @return string[]
     */
    public function getMatches()
    {
        return $this->matches;
    }

    /**
     * Gets the expanded route middleware class name or class names (if chained).
     *
     * @throws \Exception if the middleware has not been expanded.
     * @return string
     */
    public function getMiddleware()
    {
        if (!$this->expandedMiddleware) {
            throw new \Exception("Middleware must be expanded first.");
        }
        return $this->middleware;
    }

    /**
     * Gets the controller class name.
     * @return string
     */
    public function getController()
    {
        if (!$this->expandedController) {
            $this->expandController();
        }
        return $this->controller;
    }

   /**
    * Gets the controller method name (or false to indicate generic execute).
    *
    * @return string|boolean
    */
    public function getControllerArgument()
    {
        if (!$this->expandedController) {
            $this->expandController();
        }
        return $this->controllerArg;
    }

    /**
     * Checks if the route is valid for the current environment.
     *
     * @return boolean
     */
    public function isValid()
    {
        /* Check method, then check URI. */
        return $this->matchMethod() && $this->matchUri() !== false;
    }

    /**
     * Checks if the route is valid for the current request.
     *
     * @return boolean
     */
    protected function matchUri()
    {
        $request = explode('?', $_SERVER['REQUEST_URI'])[0];
        $result = preg_match("/{$this->pattern}/", $request, $matches);

        if ($result === 1) {
            return $this->matches = $matches;
        }

        return false;
    }

    /**
     * Checks if the route is valid for the current method.
     *
     * @return boolean
     */
    protected function matchMethod()
    {
        return trim(strtolower($this->method)) == trim(strtolower($_SERVER['REQUEST_METHOD']));
    }

    /**
     * Expands all aliases in the route's middleware using a reference array.
     *
     * @param array $reference The reference array containing the aliases.
     */
    public function expandMiddleware($reference)
    {
        /* Only expand once. */
        if ($this->expandedMiddleware) {
            return;
        }

        /* Resolve middleware (or chain of middleware) */
        $this->middleware = is_array($this->middleware) ? $this->middleware : [$this->middleware];

        $sanitized = [];
        foreach ($this->middleware as $m) {
            if (isset($reference[$m])) {
                $sanitized[] = $reference[$m];
            } else {
                $sanitized[] = $m;
            }
        }

        $this->middleware = $sanitized;
        $this->expandedMiddleware = true;
    }

    /**
     * Expands the controller string into class name and argument.
     */
    public function expandController()
    {
        /* Only expand controller once. */
        if ($this->expandedController) {
            return;
        }

        /* Extract controller information.
         * Controller here can also be a render engine which is handled
         * by View.
         *
         * e.g. controller+arg / engine+path
         */
        list($controller, $arg) = array_pad(explode('+', $this->controller), 2, false);

        $this->controller = $controller;
        $this->controllerArg = $arg;

        $this->expandedController = true;
    }
}
