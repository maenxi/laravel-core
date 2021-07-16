<?php
namespace Maenxi\Routing;

use Maenxi\Foundation\Application;
use Maenxi\Http\Request;

class Route
{
    protected $app;

    protected $action;

    protected $controller;

    protected $namespace = "";

    public function __construct(Application $app = null)
    {
        $this->app = $app;
    }
    public function match($path, $method)
    {
        # 获取到所有路由的保存
        $routes = $this->app->make('router', [2])->getRoutes();
        foreach ($routes[$method] as $uri => $route){
            $uri = ($uri && substr($uri,0,1) != '/') ? "/" . $uri : $uri;
            if ($path == $uri){
                $this->action = $route;
                break;
            }else{
                throw new \Exception("没有找到路由");
            }
        }
        return $this;
    }

    public function run(Request $request)
    {
        # 判断是控制器还是闭包函数
        if ($this->isControllerAction()){
            # 执行控制器方法
            return $this->runController();
        }
        # 直接执行闭包函数
        $action = $this->action;
        return $action();
    }

    public function isControllerAction()
    {
        return \is_string($this->action);
    }

    public function runController()
    {
        return $this->getController()->{$this->getMethod()}();
    }

    public function getController()
    {
        if (!$this->controller){
            $class = $this->namespace .'\\'.$this->parseControllerCallback()[0] ?? "";
            $this->controller = $this->app->make(ltrim($class,'\\'));
        }
        return $this->controller;
    }

    public function getMethod()
    {
        return $this->parseControllerCallback()[1] ?? "";
    }

    public function namespace($namespace)
    {
        $this->namespace = $namespace;
    }

    public function parseControllerCallback()
    {
        return explode('@', $this->action);
    }
}