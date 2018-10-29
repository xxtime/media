<?php

namespace Xxtime\Media\Providers;

use Xxtime\Media\Exception\ErrorException;
use Xxtime\Media\Exception\RequestException;
use Xxtime\Media\Exception\ResponseException;
use Xxtime\Media\ProviderAbstract;

/**
 * Class Weibo
 * @package Xxtime\Media\Providers
 */
class Weibo extends ProviderAbstract
{

    const LOGIN_URL = 'https://passport.weibo.cn/sso/login';


    const PASS_URL = '';


    /**
     * Weibo constructor.
     * @param array $config
     */
    public function __construct($config = ["user" => null, "password" => null, "cookies" => null])
    {
        parent::__construct($config);
        $this->setHeaders();
    }


    private function setHeaders()
    {
        // 必须存在的头信息: Content-Type, Referer
        $headers = <<<EOF
Content-Type:application/x-www-form-urlencoded
Referer:https://passport.weibo.cn/signin/login?entry=mweibo&res=wel&wm=3349&r=https%3A%2F%2Fm.weibo.cn
EOF;
        $this->httpRequest->setOptions([
            'CURLOPT_HEADER'     => true,
            'CURLOPT_USERAGENT'  => 'Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/69.0.3497.91 Mobile/15E148 Safari/605.1',
            'CURLOPT_HTTPHEADER' => explode("\n", $headers)
        ]);
    }


    // 登录
    public function login()
    {
        /**
         * 着陆页 https://passport.weibo.cn/signin/login
         */
        $data = [
            'username'     => $this->config['user'],
            'password'     => $this->config['password'],
            'savestate'    => 1,
            'r'            => 'https://m.weibo.cn/',
            'ec'           => 0,
            'pagerefer'    => 'https://m.weibo.cn/',
            'entry'        => 'mweibo',
            'wentry'       => '',
            'loginfrom'    => '',
            'client_id'    => '',
            'code'         => '',
            'qq'           => '',
            'mainpageflag' => 1,
            'hff'          => '',
            'hfp'          => '',
        ];

        $this->httpRequest->postRequest(self::LOGIN_URL, http_build_query($data));
        if ($this->httpRequest->getStatus() != 200) {
            throw new ErrorException('http code: ' . $this->httpRequest->getStatus());
        }
        $response = $this->httpRequest->getBodyObject();
        if ($response['retcode'] != 20000000) {
            throw new ResponseException($response['msg']);
        }

        $this->setCookies($this->httpRequest->getCookies());

        return true;
    }


    // 退出
    public function logout()
    {
    }


    // 修改密码
    public function password()
    {
        /**
         * 着陆页 https://security.weibo.com/account/security?topnav=1&wvr=6&option=chgpwd
         */

        if (!$this->cookie) {
            throw new RequestException('cookies is not set');
        }

        $data = [];
        $this->httpRequest->post(self::PASS_URL, http_build_query($data));
        return true;
    }


    // 发布
    public function post()
    {
    }


    // 关注
    public function follow()
    {
    }


    // 取消关注
    public function unfollow()
    {
    }


    // 访问
    public function visit()
    {
    }

}
