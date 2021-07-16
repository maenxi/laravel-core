<?php
namespace Maenxi\Foundation;

class ProviderRegister
{
    protected $app;

    public function __construct(Application $application)
    {
        $this->app = $application;
    }

    public function load($providers = [])
    {
        foreach($providers as $provider){
            $providerArray[] = $this->register($provider);
        }

        return $providerArray;
    }

    public function resolveProvider($provider)
    {
        return new $provider($this->app);
    }
    public function register($provider)
    {
        if (\is_string($provider)){
            $provider = $this->resolveProvider($provider);
        }
        $provider->register();

        return $provider;
    }
}