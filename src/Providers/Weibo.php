<?php

namespace Xxtime\Media\Providers;

use GuzzleHttp\Psr7\Request;
use Xxtime\Media\Exception\ErrorException;
use Xxtime\Media\Exception\RequestException;
use Xxtime\Media\Exception\ResponseException;
use Xxtime\Media\ProviderAbstract;
use Xxtime\Media\Utils\Tools;

/**
 * Class Weibo
 * @package Xxtime\Media\Providers
 */
class Weibo extends ProviderAbstract
{

    use Tools;


    const LOG_URL = 'https://passport.weibo.cn/sso/login';


    const CON_URL = "https://m.weibo.cn/api/config";


    const POS_URL = "https://m.weibo.cn/api/statuses/update";


    const UNR_URL = "https://m.weibo.cn/api/remind/unread";


    private $headers = null;


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
        $this->headers = [
            "User-Agent"   => 'Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/69.0.3497.91 Mobile/15E148 Safari/605.1',
            "Content-Type" => "application/x-www-form-urlencoded",
        ];
    }


    // 登录
    public function login()
    {
        $this->headers["Referer"] = "https://passport.weibo.cn/signin/login?entry=mweibo&res=wel&wm=3349&r=https%3A%2F%2Fm.weibo.cn";
        $request = new Request('GET', 'https://m.weibo.cn/', $this->headers);
        $response = $this->guzzle->send($request);
        if ($response->getStatusCode() != 200) {
            throw new ResponseException("error http code " . $response->getStatusCode());
        }
        $cookies1 = $this->getParseCookie($response->getHeader("Set-Cookie"));


        /**
         * 着陆页 https://passport.weibo.cn/signin/login
         */
        $data = [
            'username'     => $this->getConfig('user'),
            'password'     => $this->getConfig('password'),
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
        $this->headers["Cookie"] = $cookies1;
        $request = new Request('POST', self::LOG_URL, $this->headers, http_build_query($data));
        $response = $this->guzzle->send($request);
        if ($response->getStatusCode() != 200) {
            throw new ResponseException("error http code " . $response->getStatusCode());
        }
        $body = json_decode($response->getBody()->getContents(), true);
        if ($body['retcode'] != 20000000) {
            throw new ResponseException($body['msg']);
        }
        $cookies2 = $this->getParseCookie($response->getHeader("Set-Cookie"));


        // get cookie part 3 with csrf code
        $this->headers["Cookie"] .= $cookies2;
        $request = new Request('GET', self::CON_URL, $this->headers);
        $response = $this->guzzle->send($request);
        if ($response->getStatusCode() != 200) {
            throw new ResponseException("error http code " . $response->getStatusCode());
        }
        $body = json_decode($response->getBody()->getContents(), true);
        $csrf_code = $body["data"]["st"];
        $cookies3 = $this->getParseCookie($response->getHeader("Set-Cookie"));


        $this->headers["Cookie"] .= $cookies3;
        $this->setCookies($this->headers["Cookie"]);

        return true;
    }


    // 退出
    public function logout()
    {
    }


    public function password($password = "", $pass = "")
    {
        // 需要发送短信验证
        /**
         * 着陆页 https://security.weibo.com/account/security?topnav=1&wvr=6&option=chgpwd
         */

        if (!$this->getCookies()) {
            throw new RequestException('cookies is not set');
        }

        return false;
    }


    // 发布
    public function post($text = "", $att = [])
    {
        $this->headers["Referer"] = "https://m.weibo.cn/compose/";
        $this->headers["Cookie"] = $this->getCookies();

        // csrf
        if (!isset($csrf_code)) {
            $response = $this->guzzle->request("GET", self::CON_URL, ["headers" => $this->headers]);
            if ($response->getStatusCode() != 200) {
                throw new ResponseException("error http code " . $response->getStatusCode());
            }
            $body = json_decode($response->getBody()->getContents(), true);
            $csrf_code = $body["data"]["st"];
        }

        /**
         * post data
         * {"ok":1,"data":{"created_at":"Mon Oct 29 17:35:23 +0800 2018","id":"4300513258079151","mid":"4300513258079151","can_edit":false,"show_additional_indication":0,"text":"\u6211\u7684\u6d4b\u8bd5\u5185\u5bb909:35:23 ","textLength":20,"source":"\u5fae\u535a HTML5 \u7248","favorited":false,"pic_ids":[],"is_paid":false,"mblog_vip_type":0,"user":{"id":2339650945,"screen_name":"\u6700\u540e\u7684\u627f\u8bfaTheEnd","profile_image_url":"https:\/\/tva3.sinaimg.cn\/crop.0.0.640.640.180\/8b743d81jw8edkmcgx3j2j20hs0hs0tr.jpg","profile_url":"https:\/\/m.weibo.cn\/u\/2339650945?uid=2339650945","statuses_count":306,"verified":false,"verified_type":-1,"close_blue_v":false,"description":"\u7f51\u7edc\u7f16\u7a0b\u5f00\u53d1\u4e0e\u8fd0\u8425\uff0c\u5e74\u8f7b\u8d44\u6df1\u5f00\u53d1\u5de5\u7a0b\u5e08\uff0c\u6e38\u79bb\u4e8e\u9ed1\u5ba2\u4e0e\u7a0b\u5e8f\u5458\u7684\u8fb9\u7f18\uff0c80\u540e\u9893\u5e9f\u9752\u5e74\uff0c\u5531\u5c06\u7ea7K\u6b4c\u9009\u624b\uff0c\u6df7\u8ff9\u4e8e\u79bb\u9996\u90fd\u6700\u8fd1\u7684\u5317\u4eac\u57ce\uff0c\u5fc3\u5374\u603b\u662f\u5b64\u5355","gender":"m","mbtype":0,"urank":11,"mbrank":0,"follow_me":false,"following":false,"followers_count":435,"follow_count":1105,"cover_image_phone":"https:\/\/tva1.sinaimg.cn\/crop.0.0.640.640.640\/549d0121tw1egm1kjly3jj20hs0hsq4f.jpg","avatar_hd":"https:\/\/ww3.sinaimg.cn\/orj480\/8b743d81jw8edkmcgx3j2j20hs0hs0tr.jpg","like":false,"like_me":false,"badge":{"bind_taobao":1,"unread_pool":1,"unread_pool_ext":1,"user_name_certificate":1}},"reposts_count":0,"comments_count":0,"attitudes_count":0,"pending_approval_count":0,"isLongText":false,"reward_exhibition_type":0,"hide_flag":0,"visible":{"type":0,"list_id":0},"mblogtype":0,"more_info_type":0,"content_auth":0,"bid":"H0dlPxTKT"}}
         */
        $data = [
            "content" => $text,
            "st"      => $csrf_code,
        ];
        $response = $this->guzzle->request("POST", self::POS_URL, [
            "headers" => $this->headers,
            "body"    => http_build_query($data)
        ]);
        if ($response->getStatusCode() != 200) {
            throw new ResponseException("error http code " . $response->getStatusCode());
        }
        $body = json_decode($response->getBody()->getContents(), true);
        if (!isset($body["ok"]) || $body["ok"] != 1) {
            throw new ErrorException('data err: ' . $response->getBody()->getContents());
        }

        // 模拟读取
        $UNREAD_URL = self::UNR_URL . "?t=" . time() . "000";
        //$this->guzzle->request("GET", $UNREAD_URL, ["headers" => $this->headers]);

        return [
            "postId" => $body["data"]["id"]
        ];
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
