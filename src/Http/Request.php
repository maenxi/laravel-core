<?php
namespace Maenxi\Http;

class Request
{
    protected $method;
    protected $uriPath;

    public function capture()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uriPath = $_SERVER['PATH_INFO'];

        return new static();
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getUriPath()
    {
        return $this->uriPath;
    }
}