<?php

namespace Xxtime\Media;

interface ProviderInterface
{

    public function login();

    public function logout();

    public function password($password = "", $pass = "");

    public function post($text, $file);

    public function follow();

    public function unfollow();

    public function visit();

}
