<?php
/**
 * Router
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\core\routing;

use Brv\core\views\View;
use Brv\core\routing\ControllerException;
use Respect\Validation\Exceptions\ValidationException;

use Brv\core\routing\RouteCollection;

/**
 * Router
 */
class Router
{
    /**
     * Load the routing information, select the correct route and render it.
     *
     * This is the main entry point for the Router.
     */
     public static function execute()
     {
         $routes = new RouteCollection(ROUTER_CONFIG_PATH);

         /* Iterate through all valid routes. */
         foreach ($routes as $name => $route) {
             /* Resultant view i.e. endpoint. */
            $result = false;

             try {
                 /* Controller is valid. Traverse middleware. */
                 $result = self::resolveMiddleware($route->getMiddleware());
                 if ($result === false) {
                     continue;
                 }

                 if (self::isValidController($route->getController())) {

                     if ($result === true) {
                         /* All middleware passed; no new View constructed.
                          * Fallback to controller. */
                         $result = self::resolveController(
                             $route->getController(),
                             $route->getControllerArgument(),
                             $route->getMatches()
                         );

                         if ($result === null) throw new \Exception("Invalid controller.");
                     }

                     if ($result === null) throw new \Exception("Invalid view.");
                 } else {
                     // Invalid controller, assume type.
                     $result = new View($route->getControllerArgument(), [
                         'type' => $route->getController(),
                         'matches' => $route->getMatches()
                     ]);
                 }
             } catch (ControllerException $ex) {
                 $result = new View(
                    [ 'reason' => $ex->getMessage() ],
                    [ 'code' => $ex->getCode() ]
                );
             } catch (ValidationException $ex) {
                 $result = new View(
                    [ 'reason' => $ex->getMainMessage() ],
                    [ 'code' => \HTTP::BAD_PARAMS ]
                );
             } catch (\Exception $ex) {
                 http_response_code(\HTTP::SERVER);
                 return;
             }

             if ($result !== false) {
                 \App::setState(\STATES::CURRENT_ROUTE, $route);
                 \App::setState(\STATES::VIEW, $result);
                 $result->render();
                 return;
             }
         }

         /* No routes matched. */
         http_response_code(\HTTP::NOT_FOUND);
         echo "404";
     }

     /**
      * Resolve a controller and return its result.
      *
      * @param  string $controller Controller class name.
      * @param  string|boolean $arg The method name to execute on the Controller class.
      * @param  string[] $matches URL Pattern matches to pass to the Controller method.
      * @throws \Exception on invalid controller.
      * @return View|boolean
      */
     public static function resolveController($controller, $arg, $matches)
     {
         $controllerClass = "Brv\\impl\\controllers\\{$controller}";
         $instController = new $controllerClass;

         $arg = $arg === false ? 'execute' : $arg;

         if (is_callable([$instController, $arg])) {
             return call_user_func([$instController, $arg], $matches);
         }

         throw new \Exception("Invalid use of controller.");
     }

     /**
      * Determines if a class name refers to a valid controller.
      *
      * @param string $controller The class name to check.
      * @return boolean
      */
     public static function isValidController($controller)
     {
         return file_exists(NAMESPACE_IMPL_DIR . 'controllers/' . $controller . '.php');
     }

     /**
      * Determines if a class name refers to a valid middleware.
      *
      * @param string $middleware The class name to check.
      * @return boolean
      */
     public static function isValidMiddleware($middleware)
     {
         return file_exists(NAMESPACE_IMPL_DIR . 'middleware/' . $middleware . '.php');
     }

     /**
      * Resolves a chain of middleware and returns the result.
      *
      * At any point in the chain (including end result): view=false implies
      * 404 and the chain stops, view=true means to continue on to controller,
      * while view=instanceof View means to render that View.
      *
      * @param array|string $middlewares A single middleware class name or chain of class names.
      * @return View|boolean
      */
     public static function resolveMiddleware($middlewares)
     {
         // Chained left to right.
         $view = true;

         while (!empty($middlewares)) {
             if ($view === false) {
                 break;
             }

             $mw = array_shift($middlewares);

             // Fall through / pass over if mw is true.
             if ($mw === true) {
                 continue;
             }

             if (!is_array($mw)) {
                 $mw = [$mw, []];
             }

             $mwClass = "Brv\\impl\\middleware\\{$mw[0]}";
             $mwInstance = new $mwClass($view, $mw[1]);
             $view = $mwInstance->getView();
         }

         return $view;
     }
}
