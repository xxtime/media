<?php

namespace Xxtime\Media\Providers;

use GuzzleHttp\Psr7\Request;
use Xxtime\Media\Exception\ErrorException;
use Xxtime\Media\Exception\RequestException;
use Xxtime\Media\Exception\ResponseException;
use Xxtime\Media\Message\ResponsePost;
use Xxtime\Media\Message\ResponseProfile;
use Xxtime\Media\ProviderAbstract;
use Xxtime\Media\Utils\ToolsTrait;

/**
 * Class Weibo
 * @package Xxtime\Media\Providers
 */
class Weibo extends ProviderAbstract
{

    use ToolsTrait;


    const URL_LOG = 'https://passport.weibo.cn/sso/login';


    const URL_CON = "https://m.weibo.cn/api/config";


    const URL_POS = "https://m.weibo.cn/api/statuses/update";


    const URL_UNR = "https://m.weibo.cn/api/remind/unread";


    const URL_UPL = "https://m.weibo.cn/api/statuses/uploadPic";


    private $headers = null;


    private $csrf = null;


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
        $request = new Request('POST', self::URL_LOG, $this->headers, http_build_query($data));
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
        $request = new Request('GET', self::URL_CON, $this->headers);
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


    public function profile($uid = null)
    {
        if (!$uid) {
            throw new RequestException("no uid set");
        }
        // warning: use computer user-agent, not mobile user-agent
        $this->headers["Referer"] = "https://weibo.com/p/{$uid}?is_all=1";
        $this->headers["Cookie"] = $this->getCookies();
        $this->headers["User-Agent"] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36";
        $url = "https://weibo.com/p/{$uid}/info";
        $request = new Request('GET', $url, $this->headers);
        $response = $this->guzzle->send($request);

        $html = $response->getBody()->getContents();
        $data = [];

        preg_match('/昵称(.*)(pt_detail)(.*)(>)(.*)(<)/U', $html, $matches);
        if ($matches) {
            $data["name"] = $matches['5'];
        }

        preg_match('/真实姓名(.*)(pt_detail)(.*)(>)(.*)(<)/U', $html, $matches);
        if ($matches) {
            $data["realName"] = $matches['5'];
        }

        preg_match('/性别(.*)(pt_detail)(.*)(>)(.*)(<)/U', $html, $matches);
        if ($matches) {
            $data["gender"] = $matches['5'];
        }

        // TODO :: 部分用户生日格式正确 eg., https://weibo.com/p/1003061474398241/info
        preg_match('/生日(.*)(pt_detail)(.*)(>)(.*)(<)/U', $html, $matches);
        if ($matches) {
            $date = date_parse_from_format('Y年m月d日', $matches['5']);
            $data["birthday"] = "{$date['year']}-" . sprintf("%'.02d", $date['month']) . "-{$date['day']}";
        }

        preg_match('/所在地(.*)(pt_detail)(.*)(>)(.*)(<)/U', $html, $matches);
        if ($matches) {
            $data["locale"] = $matches['5'];
        }

        preg_match('/简介(.*)(pt_detail)(.*)(>)(.*)(<)/U', $html, $matches);
        if ($matches) {
            $data["desc"] = $matches['5'];
        }

        preg_match('/注册时间(.*)(pt_detail)(.*)(>)(.*)(<)/U', $html, $matches);
        if ($matches) {
            $data["createTime"] = strtotime(trim($matches['5'], " ,\\r\\n,\r\n"));
        }

        preg_match('/photo_wrap(.*)(img src=)(.*)( alt)/U', $html, $matches);
        if ($matches) {
            $data["avatar"] = 'http://' . trim(str_replace(['"', '\\'], '', $matches['3']), "/,\ ");
        }

        preg_match('/当前等级(.*)(S_txt1)(.*)(Lv.)(.*)(<)/U', $html, $matches);
        if ($matches) {
            $data["level"] = $matches['5'];
        }

        $data["tags"] = null;
        $data["followNum"] = null;
        $data["followersNum"] = null;
        $data["postsNum"] = null;

        return new ResponseProfile($data);
    }


    /**
     * 发布
     * @param array $data
     * @return array|bool
     * @throws ErrorException
     * @throws ResponseException
     *
     * return data
     * {"ok":1,"data":{"created_at":"Mon Oct 29 17:35:23 +0800 2018","id":"4300513258079151","mid":"4300513258079151",
     * "can_edit":false,"show_additional_indication":0,"text":"\u6211\u7684\u6d4b\u8bd5\u5185\u5bb909:35:23 ",
     * "textLength":20,"source":"\u5fae\u535a HTML5 \u7248","favorited":false,"pic_ids":[],"is_paid":false,
     * "mblog_vip_type":0,"user":{"id":2339650945,"screen_name":"\u6700\u540e\u7684\u627f\u8bfaTheEnd",
     * "profile_image_url":"https:\/\/tva3.sinaimg.cn\/crop.0.0.640.640.180\/8b743d81jw8edkmcgx3j2j20hs0hs0tr.jpg",
     * "profile_url":"https:\/\/m.weibo.cn\/u\/2339650945?uid=2339650945","statuses_count":306,"verified":false,
     * "verified_type":-1,"close_blue_v":false,"description":"DESC","gender":"m","mbtype":0,"urank":11,"mbrank":0,
     * "follow_me":false,"following":false,"followers_count":435,"follow_count":1105,
     * "cover_image_phone":"https:\/\/tva1.sinaimg.cn\/crop.0.0.640.640.640\/549d0121tw1egm1kjly3jj20hs0hsq4f.jpg",
     * "avatar_hd":"https:\/\/ww3.sinaimg.cn\/orj480\/8b743d81jw8edkmcgx3j2j20hs0hs0tr.jpg","like":false,
     * "like_me":false,"badge":{"bind_taobao":1,"unread_pool":1,"unread_pool_ext":1,"user_name_certificate":1}},
     * "reposts_count":0,"comments_count":0,"attitudes_count":0,"pending_approval_count":0,"isLongText":false,
     * "reward_exhibition_type":0,"hide_flag":0,"visible":{"type":0,"list_id":0},"mblogtype":0,"more_info_type":0,
     * "content_auth":0,"bid":"H0dlPxTKT"}}
     */
    public function post(array $data)
    {
        if (!isset($data["content"])) {
            return false;
        }
        $this->headers["Referer"] = "https://m.weibo.cn/compose/";
        $this->headers["Cookie"] = $this->getCookies();

        $postData = [
            "content" => $data["content"],
            "st"      => $this->getCsrfCode(),
        ];

        // 上传图片
        if (isset($data["picture"]) && is_array($data["picture"])) {
            $picture = [];
            foreach ($data["picture"] as $resource) {
                $body = $this->upload($resource);
                $picture[] = $body["pic_id"];
            }
            $postData["picId"] = implode(",", $picture);
        }

        // 开始发布
        $this->headers["Content-Type"] = "application/x-www-form-urlencoded";
        $response = $this->guzzle->request("POST", self::URL_POS, [
            "headers" => $this->headers,
            "body"    => http_build_query($postData)
        ]);
        if ($response->getStatusCode() != 200) {
            throw new ResponseException("error http code " . $response->getStatusCode());
        }
        $body = json_decode($response->getBody()->getContents(), true);
        if (!isset($body["ok"]) || $body["ok"] != 1) {
            throw new ErrorException('data err: ' . $response->getBody()->getContents());
        }

        // 模拟读取
        $UNREAD_URL = self::URL_UNR . "?t=" . time() . "000";
        //$this->guzzle->request("GET", $UNREAD_URL, ["headers" => $this->headers]);


        return new ResponsePost([
            "id"      => $body["data"]["id"],
            "content" => $data["content"],
        ]);
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


    private function getCsrfCode()
    {
        if (!$this->csrf) {
            $response = $this->guzzle->request("GET", self::URL_CON, ["headers" => $this->headers]);
            if ($response->getStatusCode() != 200) {
                throw new ResponseException("error http code " . $response->getStatusCode());
            }
            $body = json_decode($response->getBody()->getContents(), true);
            $this->csrf = $body["data"]["st"];
        }
        return $this->csrf;
    }


    private function upload($resource)
    {
        if (!is_resource($resource)) {
            throw new ErrorException("it`s not a stream resource");
        }
        // guzzle auto set Content-Type
        unset($this->headers["Content-Type"]);
        $response = $this->guzzle->request('POST', self::URL_UPL, [
            "headers"   => $this->headers,
            'multipart' => [
                [
                    'name'     => 'type',
                    'contents' => 'json',
                ],
                [
                    'name'     => 'st',
                    'contents' => $this->getCsrfCode(),
                ],
                [
                    'name'     => 'pic',
                    'contents' => $resource,
                ]
            ]
        ]);
        if ($response->getStatusCode() != 200) {
            throw new ErrorException("file upload failed");
        }
        return json_decode($response->getBody()->getContents(), true);
    }

}
