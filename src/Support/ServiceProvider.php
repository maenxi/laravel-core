<?php
namespace Maenxi\Support;
use Maenxi\Foundation\Application;

abstract class ServiceProvider
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function register()
    {

    }

    public function boot()
    {

    }
}
