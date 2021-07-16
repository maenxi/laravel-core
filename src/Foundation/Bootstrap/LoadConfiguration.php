<?php
namespace Maenxi\Foundation\Bootstrap;
use Maenxi\Foundation\Application;

class LoadConfiguration
{
    public function bootstrap(Application $app)
    {
        $app->instance('config', config());
    }
}