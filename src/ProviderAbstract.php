<?php

namespace Xxtime\Media;

use Xxtime\Media\Message\HttpRequest;
use GuzzleHttp\Client;

abstract class ProviderAbstract implements ProviderInterface
{

    protected $config;


    protected $cookie;


    protected $httpRequest;


    protected $guzzle;


    protected function __construct(array $config)
    {
        // first init httpRequest
        $this->httpRequest = new HttpRequest();
        // guzzle will replace httpRequest
        $this->guzzle = new Client([
            'timeout' => 5,
        ]);

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


    public function setCookies($cookies = "")
    {
        $this->cookie = $cookies;
        $this->httpRequest->setOptions([
            'CURLOPT_COOKIE' => $cookies
        ]);
    }

}
