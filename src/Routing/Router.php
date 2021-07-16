<?php
namespace Maenxi\Routing;
use Maenxi\Contracts\Routing\BindingRegistrar;
use Maenxi\Contracts\Routing\Registrar as RegistrarContract;
use Maenxi\Foundation\Application;
use Maenxi\Http\Request;


class Router implements BindingRegistrar, RegistrarContract
{
    protected $app;
    protected $routes = [];
    protected $route;
    public static $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
    public function __construct(Application $app = null)
    {
        $this->app = $app;
        $this->route = new Route($app);
    }

    public function get($uri, $action = null)
    {
        $this->addRoute(['GET', 'HEAD'], $uri, $action);
    }

    public function post($uri, $action = null)
    {
        $this->addRoute([__FUNCTION__], $uri, $action);
    }

    public function put($uri, $action = null)
    {
        $this->addRoute([__FUNCTION__], $uri, $action);
    }

    public function delete($uri, $action = null)
    {
        $this->addRoute([__FUNCTION__], $uri, $action);
    }

    public function patch($uri, $action = null)
    {
        $this->addRoute([__FUNCTION__], $uri, $action);
    }

    public function any($uri, $action = null)
    {
        return $this->addRoute(self::$verbs, $uri, $action);
    }

    public function resources(array $resources, array $options = [])
    {
        foreach ($resources as $name => $controller) {
            $this->resource($name, $controller, $options);
        }
    }

    public function resource($name, $controller, array $options = []){

    }

    public function options($uri, $action = null)
    {
        $this->addRoute([__FUNCTION__], $uri, $action);
    }

    public function match($methods, $uri, $action = null)
    {
        $methods = is_array($methods) ? $methods : [$methods];
        $this->addRoute($methods, $uri, $action);
    }

    public function addRoute($methods, $uri, $action = null)
    {
        foreach ($methods as $method){
            $this->routes[strtoupper($method)][$uri] = $action;
        }
    }

    public function dispatcher(Request $request)
    {
        return $this->runRoute($request, $this->findRoute($request));
    }

    public function findRoute(Request $request)
    {
        return $this->route->match($request->getUriPath(), $request->getMethod());
    }

    public function runRoute(Request $request, Route $route)
    {
        return $route->run($request);
    }

    /**
     * @return array|Route
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    public function bind($key, $binder)
    {
        // TODO: Implement bind() method.
    }

    public function getBindingCallback($key)
    {
        // TODO: Implement getBindingCallback() method.
    }

    public function group(array $attributes, $routes)
    {
        // TODO: Implement group() method.
    }

    public function substituteBindings($route)
    {
        // TODO: Implement substituteBindings() method.
    }

    public function substituteImplicitBindings($route)
    {
        // TODO: Implement substituteImplicitBindings() method.
    }
}