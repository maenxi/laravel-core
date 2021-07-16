<?php
namespace Maenxi\Foundation;
use Maenxi\Container\Container;

class Application extends Container
{
    # 框架根目录
    protected $basePath;
    # 服务提供
    protected $serviceProviders = [];
    # 服务启动开关
    protected $booted = false;

    public function __construct($basePath)
    {
        if ($basePath){
            $this->setBasePath($basePath);
        }

    }

    public function boot()
    {
        if(!$this->booted){
            foreach ($this->serviceProviders as $provider){
                if(method_exists($provider, 'boot')){
                    $provider->boot;
                }
            }
            $this->booted = true;
        }
    }

    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
    }

    public function getBasePath()
    {
        return $this->basePath;
    }

    public function registerBaseBindings()
    {
        self::setInstance($this);
        $this->instance('app', $this);
    }

    public function registerConfiguredProviders()
    {

    }

    public function marASRegisterProviders($providers)
    {
        foreach ($providers as $provider){
            $this->serviceProviders[] = $provider;
        }
    }

    public function registerCoreContainerAliases()
    {

    }
}