<?php

namespace Xxtime\Media;

use Xxtime\Media\Message\HttpRequest;

abstract class ProviderAbstract implements ProviderInterface
{

    protected $config;


    protected $cookie;


    protected $httpRequest;


    protected function __construct($config = [])
    {
        $this->config = $config;
        $this->httpRequest = new HttpRequest();
        if (!empty($config['cookies'])) {
            $this->setCookies($config['cookies']);
        }
    }


    protected function getCookies()
    {
        return $this->cookie;
    }


    protected function setCookies($cookies)
    {
        $this->cookie = $cookies;
        $this->httpRequest->setOptions([
            'CURLOPT_COOKIE' => $cookies
        ]);
    }

}
