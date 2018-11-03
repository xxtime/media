<?php

namespace Xxtime\Media;

interface ProviderInterface
{

    public function login();

    public function password($password = "", $pass = "");

    public function post(array $data);

    public function follow($uid = "");

    public function unfollow($uid = "");

    public function getProfile($uid = "");

    public function getPosts(array $data);

    public function getFollowing(array $data);

    public function getFollowers(array $data);

}
