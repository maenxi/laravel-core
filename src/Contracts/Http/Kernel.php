<?php
namespace Maenxi\Contracts\Http;
interface Kernel
{
    public function bootstrap();
    public function handle($request);
}