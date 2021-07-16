<?php
namespace Maenxi\Container;
use Maenxi\Contracts\Container\Container as ContainerContract;
use TypeError;

class Container implements \ArrayAccess, ContainerContract
{
    # 容器的共享实例
    protected $instances = [];
    # 已解析的类型
    protected $resolved = [];
    # 容器的绑定
    protected $bindings = [];
    # 已注册的类型别名
    protected $aliases = [];
    # 以抽象名称为键的注册别名
    protected $abstractAliases = [];
    # 全局可用的容器
    protected static $instance = [];
    # 所有注册的反弹回调
    protected $reboundCallbacks = [];

    public static function getInstance()
    {
        if(!self::$instance){
            self::$instance = new static;
        }

        return self::$instance;
    }
    public static function setInstance($instance)
    {
        self::$instance = $instance;
    }
    public function instance($abstract, $instance)
    {
        $this->instances[$abstract] = $instance;
    }
    /**
     * 注册一个绑定到容器.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     *
     * @throws \TypeError
     */
    public function bind($abstract, $concrete = null, $share = false)
    {
        $this->dropStaleInstances($abstract);
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        if (! $concrete instanceof \Closure) {
            if (! is_string($concrete)) {
                throw new TypeError(self::class.'::bind(): Argument #2 ($concrete) must be of type Closure|string|null');
            }

            $concrete = $this->getClosure($abstract, $concrete);
        }

        $this->bindings[$abstract] = compact('concrete', 'shared');
        if ($this->resolved($abstract)) {
            $this->rebound($abstract);
        }
    }
    /**
     * 获取构建类型时要使用的Closure
     *
     * @param  string  $abstract
     * @param  string  $concrete
     * @return \Closure
     */
    protected function getClosure($abstract, $concrete)
    {
        return function ($container, $parameters = []) use ($abstract, $concrete) {
            if ($abstract == $concrete) {
                return $container->build($concrete);
            }

            return $container->resolve(
                $concrete, $parameters, $raiseEvents = false
            );
        };
    }
    /**
     * 为给定的抽象类型触发“反弹”回调
     *
     * @param  string  $abstract
     * @return void
     */
    protected function rebound($abstract)
    {
        $instance = $this->make($abstract);

        foreach ($this->getReboundCallbacks($abstract) as $callback) {
            call_user_func($callback, $this, $instance);
        }
    }
    /**
     * 获取给定类型的回调
     *
     * @param  string  $abstract
     * @return array
     */
    protected function getReboundCallbacks($abstract)
    {
        return $this->reboundCallbacks[$abstract] ?? [];
    }
    /**
     * 删除已绑定的实例和别名
     *
     * @param  string  $abstract
     * @return void
     */
    protected function dropStaleInstances($abstract)
    {
        unset($this->instances[$abstract], $this->aliases[$abstract]);
    }
    /**
     * 在容器中注册一个共享绑定
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }
    /**
     * 判断
     * */
    public function has($id)
    {
        return $this->bound($id);
    }
    /**
     * 给定的抽象类型是否已经绑定
     *
     * @param string $abstract
     * @return bool
     * */
    public function bound($abstract)
    {
        return isset($this->bindings[$abstract]) ||
               isset($this->instances[$abstract]) ||
               $this->isAlias($abstract);
    }
    /**
     * 确定给定字符串是否为别名
     *
     * @param  string  $name
     * @return bool
     */
    public function isAlias($name)
    {
        return isset($this->aliases[$name]);
    }
    /**
     * 从容器中解析给定类型
     *
     * @param  string|callable  $abstract
     * @param  array  $parameters
     * @return mixed
     */
    public function make($abstract, array $parameters = [])
    {
        return $this->resolve($abstract, $parameters);
    }
    /**
     * 解析绑定
     *
     * @param  string|callable  $abstract
     * @param  array  $parameters
     * @return mixed
     */
    protected function resolve($abstract, $parameters = [])
    {
        $abstract = $this->getAlias($abstract);

        if(isset($this->instances[$abstract])){
            return $this->instances[$abstract];
        }

        $object = $this->getConcrete($abstract);

        if($object instanceof \Closure){
            return $object();
        }

        if(!is_object($object)){
            $object = new $object(...$parameters);
        }

        $this->resolved[$abstract] = true;
        $this->instances[$abstract] = $object;

        return $object;
    }
    /**
     * 获取给定抽象的具体类型
     *
     * @param  string|callable  $abstract
     * @return mixed
     */
    protected function getConcrete($abstract)
    {
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]['concrete'];
        }

        return $abstract;
    }
    /**
     * 设置别名
     *
     * @param string $abstract
     * @param string $alias
     * @return void
     *
     * @throws \LogicException
     * */
    public function alias($abstract, $alias)
    {
        if($abstract === $alias){
            throw new \LogicException("[{$abstract}] is aliased to itself.");
        }

        $this->aliases[$alias] = $abstract;

        $this->abstractAliases[$abstract][] = $alias;
    }
    public function getAlias($abstract)
    {
        return isset($this->aliases[$abstract])
               ? $this->getAlias($this->aliases[$abstract])
               : $abstract;
    }
    /**
     * 确定给定的抽象类型是否已解析
     *
     * @param string $abstract
     * @return bool
     */
    public function resolved($abstract)
    {
        if ($this->isAlias($abstract)) {
            $abstract = $this->getAlias($abstract);
        }

        return isset($this->resolved[$abstract]) ||
               isset($this->instances[$abstract]);
    }
    public function offsetExists($key)
    {
        return $this->bound($key);
    }
    public function offsetGet($key)
    {
        return $this->make($key);
    }
    public function offsetSet($key, $value)
    {
        $this->bind($key, $value instanceof \Closure ? $value : function () use ($value) {
            return $value;
        });
    }
    public function offsetUnset($key)
    {
        unset($this->bindings[$key], $this->instances[$key], $this->resolved[$key]);
    }
}