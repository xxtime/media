<?php

namespace Xxtime\Media;

use GuzzleHttp\Client;

abstract class ProviderAbstract implements ProviderInterface
{


    private $config;


    private $cookie;


    protected $guzzle;


    protected function __construct(array $config)
    {
        // @see http://docs.guzzlephp.org
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


    protected function getConfig($key = null)
    {
        if ($key == null) {
            return $this->config;
        }
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }
        return null;
    }


    public function getCookies()
    {
        return $this->cookie;
    }


    public function setCookies($cookies = "")
    {
        return $this->cookie = $cookies;
    }

}
