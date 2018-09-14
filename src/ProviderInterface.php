<?php

namespace Xxtime\Media;

interface ProviderInterface
{

    public function login();

    public function logout();

    public function password();

    public function post();

    public function follow();

    public function unfollow();

    public function visit();

}
