<?php

namespace Xxtime\Media;

use Xxtime\Media\Message\HttpRequest;

abstract class ProviderAbstract implements ProviderInterface
{

    protected $config;

    protected $cookie;

    protected $httpRequest;

    public function __construct($config = [])
    {
        $this->httpRequest = new HttpRequest();
    }

}
