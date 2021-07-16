<?php
namespace Maenxi\Foundation\Bootstrap;
use Maenxi\Foundation\Application;

class BootProviders
{
    public function bootstrap(Application $app)
    {
        $app->boot();
    }
}