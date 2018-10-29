<?php

namespace Xxtime\Media\Message;


use Xxtime\Media\Exception\CurlException;

class HttpRequest
{

    /**
     * http response code
     * @var int
     */
    private $status = 0;


    /**
     * response header
     * @var string
     */
    private $header = '';


    /**
     * response body
     * @var string
     */
    private $body = '';


    /**
     * curl_getinfo data
     * @var null
     */
    private $reqInfo = null;


    /**
     * default curl options
     * application/x-www-form-urlencoded
     * application/json
     * application/octet-stream
     * multipart/form-data
     */
    protected $options = [
        "CURLINFO_HEADER_OUT"    => true,
        "CURLOPT_CONNECTTIMEOUT" => 10,
        "CURLOPT_TIMEOUT"        => 30,
        "CURLOPT_HEADER"         => false,
        "CURLOPT_RETURNTRANSFER" => true,
        "CURLOPT_AUTOREFERER"    => true,
        "CURLOPT_FOLLOWLOCATION" => true,
        "CURLOPT_MAXREDIRS"      => 5,
        "CURLOPT_USERAGENT"      => "CurlUtils (XT) https://blog.xxtime.com",
        "CURLOPT_HTTPHEADER"     => [
            "Accept-Language: en-US,en;q=0.9,zh-CN;q=0.8,zh;q=0.7",
            "Content-Type: application/x-www-form-urlencoded;charset=utf-8",
            //"Cookie: locale=en_US",
        ]
    ];


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

        $this->reqInfo = curl_getinfo($ch);
        $this->status = $this->reqInfo['http_code'];
        if (!$this->status) {
            curl_close($ch);
            throw new CurlException('error request: ' . $this->reqInfo['url']);
        }

        curl_close($ch);
        list($this->header, $this->body) = explode("\r\n\r\n", $output);
        return $this->body;
    }


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
    public function getRequest($url = '', $data = null)
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
    public function postRequest($url = '', $data = [])
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
     * get response status
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }


    /**
     * get response header
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }


    /**
     * get response body - RAW
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }


    /**
     * get request header info
     * @return array
     */
    public function getReqInfo()
    {
        return $this->reqInfo;
    }


    /**
     * get response body - OBJECT
     * @return string
     */
    public function getBodyObject()
    {
        return json_decode($this->body, true);
    }


    /**
     * get response cookies
     * @return string
     */
    public function getCookies()
    {
        preg_match_all('/Set-Cookie:[^;]+;/i', $this->getHeader(), $cookies);
        $result = '';
        if ($cookies) {
            foreach ($cookies['0'] as $cookie) {
                $result .= substr($cookie, 12);
            }
        }
        return $result;
    }

}
