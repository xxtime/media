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


    public function getCookies()
    {
        return $this->cookie;
    }


    public function setCookies($cookies)
    {
        if (is_array($cookies)) {
            $cookies = $cookies['0'];
        }
        $this->cookie = $cookies;
        $this->httpRequest->setOptions([
            'CURLOPT_COOKIE' => $cookies
        ]);
    }

}
