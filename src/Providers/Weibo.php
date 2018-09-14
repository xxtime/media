<?php

namespace Xxtime\Media\Providers;

use Xxtime\Media\ProviderAbstract;

class Weibo extends ProviderAbstract
{

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    // 登录
    public function login()
    {
    }

    // 退出
    public function logout()
    {
    }

    // 修改密码
    public function password()
    {
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
