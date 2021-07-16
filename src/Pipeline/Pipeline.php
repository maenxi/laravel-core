<?php
namespace Maenxi\Pipeline;
use Maenxi\Contracts\Pipeline\Pipeline as PipelineContract;
use Maenxi\Foundation\Application;

class Pipeline implements PipelineContract
{
    protected $app;
    # 中间件数组
    protected $pipelines;
    # 想要执行的方法
    protected $method = "handle";
    protected $passable;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function then(\Closure $closure)
    {
        $pipeline = array_reduce(
            $this->pipes(),
            $this->carry(),
            $closure
        );

        return $pipeline($this->passable);
    }

    public function pipes()
    {
        return $this->pipelines;
    }

    public function carry()
    {
        return function ($stack, $pipe){
            return function ($passable) use ($stack, $pipe){
                # 判断是否为可回调
                if (is_callable($pipe)){
                    return $pipe($passable, $stack);
                }else if (!is_object($pipe)){
                    $pipe = $this->app->make($pipe);
                    $parameter = [$passable, $stack];
                }
                return method_exists($pipe, $this->method) ? $pipe->{$this->method}(...$parameter) : $pipe(...$pipe);
            };
        };
    }

    public function send($passable)
    {
        $this->passable = $passable;

        return $this;
    }

    public function through($pipes)
    {
        $this->pipes = is_array($pipes) ? $pipes : func_get_args();

        return $this;
    }

    public function via($method)
    {
        $this->method = $method;

        return $this;
    }
}