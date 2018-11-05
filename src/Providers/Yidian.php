<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/11/2
 * Time: 1:12 PM
 */

namespace Xxtime\Media\Providers;

use GuzzleHttp\Psr7\Request;
use Xxtime\Media\Exception\ResponseException;
use Xxtime\Media\ProviderAbstract;
use Xxtime\Media\Utils\ToolsTrait;

class Yidian extends ProviderAbstract
{
    use ToolsTrait;

    const URL_LOG = 'https://www.yidianzixun.com/mp_sign_in';

    private $headers = null;

    /**
     *  初始化header
     */
    //https://www.toutiao.com/?ticket=e93a6d697fd77727c8b769e33d29af39
    private function setHeaders()
    {
        $this->headers = [
            "user-agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36",
            "content-type" => "application/x-www-form-urlencoded; charset=UTF-8",
            "referer" => "https://www.yidianzixun.com/",
            "pragma" => "no-cache",
            "cache-control" => "no-cache",
            "accept" => "*/*",
            "origin" => "https://www.yidianzixun.com",
            "x-requested-with" => "XMLHttpRequest",
            "referer" => "https://www.yidianzixun.com/profile",
            "accept-encoding" => "gzip, deflate, br",
            "accept-language" => "zh-CN,zh;q=0.9,en;q=0.8,th;q=0.7"
        ];
    }

    public function __construct($config = ["user" => null, "password" => null, "cookies" => null])
    {
        parent::__construct($config);
        $this->setHeaders();
    }


    /*
      * [
      *      "userid" => "1395992734@qq.com",
      *      "nickname" => "一点网友1395992734",
      *      "version" => "999999",
      *      "usertype" => "login",
      *      "freshuser" => false,
      *      "profile_url" => "http://s.go2yd.com/a/head_xiongmao.png",
      *      "isbindmobile" => false,
      *      "cookie" => "JSESSIONID=YLqZW99fLvzvEUAr2ty67g",
      *      "utk" => "c9r6gh6r",
      *      "status" => "success",
      *      "code" => 0
      * ]
      */
    public function login()
    {
        $data = [
            'username' => $this->getConfig("username"),
            'password' => $this->getConfig("password")
        ];
        $request = new Request("post", self::URL_LOG, $this->headers, http_build_query($data));
        $response = $this->guzzle->send($request);
        if ($response->getStatusCode() != 200) {
            throw new ResponseException("error http code " . $response->getStatusCode());
        }
        $body = json_decode($response->getBody()->getContents(), true);
        if ($body['code'] != 0) {
            throw new ResponseException($body['status']);
        }

        $cookie = $body['cookie'];
        $utk = $body['utk'];
        $this->headers['cookie'] = $cookie;
        $this->headers['utk'] = $utk;

        $this->setCookies($this->headers["cookie"]);

        return true;
    }

    public function logout()
    {
        // TODO: Implement logout() method.
    }

    public function password($password = "", $pass = "")
    {
        // TODO: Implement password() method.
    }

    public function post(array $data)
    {
        // TODO: Implement post() method.
    }

    public function visit()
    {
        // TODO: Implement visit() method.
    }

    public function getPosts(array $data)
    {
        // TODO: Implement getPosts() method.
    }

    public function getProfile($uid = "")
    {
        // TODO: Implement getProfile() method.
    }

    public function getFollowers(array $data)
    {
        // TODO: Implement getFollowers() method.
    }

    public function getFollowing(array $data)
    {
        // TODO: Implement getFollowing() method.
    }

    public function follow($uid = "")
    {
        // TODO: Implement follow() method.
    }

    public function unfollow($uid = "")
    {
        // TODO: Implement unfollow() method.
    }
}