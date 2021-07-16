<?php
use Maenxi\Foundation\Application;
use Maenxi\Config\Config;
## 助手函数

if(!function_exists('app')){
    function app($abstract = null, $parameters = [])
    {
        if(is_null($abstract)){
            return Application::getInstance();
        }

        return Application::getInstance()->make($abstract,$parameters);
    }
}

if(!function_exists('config')){
    function config($sConfigKey = null)
    {
        return Config::getInstance()->get($sConfigKey);
    }
}