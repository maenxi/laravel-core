<?php
namespace Maenxi\Foundation\Http;
use \Maenxi\Contracts\Http\Kernel as KernelContract;
use Maenxi\Foundation\Application;
use Maenxi\Foundation\Bootstrap\BootProviders;
use Maenxi\Foundation\Bootstrap\LoadConfiguration;
use Maenxi\Foundation\Bootstrap\RegisterFacades;
use Maenxi\Foundation\Bootstrap\RegisterProviders;

class Kernel implements KernelContract
{
    protected $app;
    # 启动项目所需注册的服务
    protected $bootstrappers = [
        LoadConfiguration::class,
        RegisterFacades::class,
        RegisterProviders::class,
        BootProviders::class,
    ];
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle($request)
    {
        $this->sendRequestThroughRouter($request);
    }

    protected function sendRequestThroughRouter($request = null)
    {
        # 引导文件
        $this->bootstrap();

        $this->app->instance('request', $request);
    }

    public function bootstrap()
    {
        foreach ($this->bootstrappers as $bootstrapper){
            $this->app->make($bootstrapper)->bootstrap($this->app);
        }
    }
}