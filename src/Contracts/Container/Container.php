<?php
namespace Maenxi\Contracts\Container;
interface Container
{
    public function bound($abstract);
    public function alias($abstract, $alias);
    public function bind($abstract, $concrete = null, $shared = false);
    public function singleton($abstract, $concrete = null);
    public function instance($abstract, $instance);
    public function make($abstract, array $parameters = []);
    public function resolved($abstract);
}