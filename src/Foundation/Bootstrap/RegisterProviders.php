<?php
namespace Maenxi\Foundation\Bootstrap;
use Maenxi\Foundation\Application;

class RegisterProviders
{
    public function bootstrap(Application $app)
    {
        $app->registerConfiguredProviders();
    }
}