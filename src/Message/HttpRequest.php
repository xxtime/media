<?php

namespace Xxtime\Media\Message;


use Xxtime\Media\Exception\CurlException;

class HttpRequest
{

    /**
     * default curl options
     * application/x-www-form-urlencoded
     * application/json
     * application/octet-stream
     * multipart/form-data
     */
    protected $options = [
        "CURLOPT_CONNECTTIMEOUT" => 10,
        "CURLOPT_TIMEOUT"        => 30,
        "CURLOPT_HEADER"         => false,
        "CURLOPT_RETURNTRANSFER" => true,
        "CURLOPT_AUTOREFERER"    => true,
        "CURLOPT_FOLLOWLOCATION" => true,
        "CURLOPT_MAXREDIRS"      => 5,
        "CURLOPT_COOKIEFILE"     => null,
        "CURLOPT_COOKIEJAR"      => null,
        "CURLOPT_USERAGENT"      => "CurlUtils (XT) https://blog.xxtime.com",
        "CURLOPT_HTTPHEADER"     => [
            "Accept-Language: en-US,en;q=0.9,zh-CN;q=0.8,zh;q=0.7",
            "Content-Type: application/x-www-form-urlencoded;charset=utf-8",
            //"Cookie: locale=en_US",
        ]
    ];


    /**
     * set options
     * @param array $options
     * @return bool
     */
    public function setOptions($options = [])
    {
        if (!is_array($options)) {
            return false;
        }
        foreach ($options as $opt => $value) {
            $this->options[$opt] = $value;
        }
    }


    /**
     * get options
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }


    /**
     * get method
     * @param string $url
     * @param null $data
     * @return bool|mixed
     * @throws CurlException
     */
    public function get($url = '', $data = null)
    {
        if ($data) {
            if (!is_array($data)) {
                return false;
            }
            if (strpos($url, '?')) {
                $url .= '&' . http_build_query($data);
            }
            else {
                $url .= '?' . http_build_query($data);
            }
        }
        $options = $this->options;
        $options["CURLOPT_HTTPGET"] = true;
        $output = $this->curlInit($url, $options);
        return $output;
    }


    /**
     * post method
     * @param string $url
     * @param array $data
     * @return mixed
     * @throws CurlException
     */
    public function post($url = '', $data = [])
    {
        $options = $this->options;
        $options["CURLOPT_POST"] = true;
        if ($data) {
            $options["CURLOPT_POSTFIELDS"] = $data;
        }
        $output = $this->curlInit($url, $options);
        return $output;
    }


    /**
     * curl init
     * @param string $url
     * @param array $options
     * @return mixed
     * @throws CurlException
     */
    protected function curlInit($url = '', $options = [])
    {
        $optionsFormat = [];
        foreach ($options as $key => $value) {
            $optionsFormat[constant($key)] = $value;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, str_replace(' ', '+', trim($url)));
        curl_setopt_array($ch, $optionsFormat);
        $output = curl_exec($ch);

        $info = curl_getinfo($ch);
        if ($info['http_code'] != 200) {
            curl_close($ch);
            throw new CurlException('response http_code: ' . $info['http_code']);
        }

        curl_close($ch);
        return $output;
    }

}
