<?php

namespace Xxtime\Media;

use Xxtime\Media\Message\HttpRequest;

abstract class ProviderAbstract implements ProviderInterface
{

    protected $config;


    protected $cookie;


    protected $httpRequest;


    protected function __construct(array $config)
    {
        // first init httpRequest
        $this->httpRequest = new HttpRequest();

        // second set config
        if (!empty($config['cookies'])) {
            $this->setCookies($config['cookies']);
            unset($config['cookies']);
        }
        $this->config = $config;
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
