<?php

namespace Xxtime\Media\Utils;


trait ToolsTrait
{

    /**
     * @param array $cookies array from $response->getHeader("Set-Cookie") in guzzle
     * @return string
     */
    function getParseCookie($cookies = [])
    {
        $result = "";
        foreach ($cookies as $line) {
            $off = strpos($line, ";");
            $result .= substr($line, 0, $off + 1) . ' ';
        }
        return $result;
    }

}
