<?php

namespace Xxtime\Media\Message;


class ResponseProfile extends MessageAbstract
{

    public function __construct(array $data)
    {
        parent::__construct($data);
    }

    public function getName()
    {
        return $this->getAttribute("name");
    }

    public function getRealName()
    {
        return $this->getAttribute("realName");
    }

    public function getGender()
    {
        return $this->getAttribute("gender");
    }

    public function getBirthday()
    {
        return $this->getAttribute("birthday");
    }

    public function getLocale()
    {
        return $this->getAttribute("locale");
    }

    public function getMobile()
    {
        return $this->getAttribute("mobile");
    }

    public function getAvatar()
    {
        return $this->getAttribute("avatar");
    }

    public function getCollege()
    {
        return $this->getAttribute("college");
    }

    public function getDesc()
    {
        return $this->getAttribute("desc");
    }

    public function getUrl()
    {
        return $this->getAttribute("url");
    }

    public function getTags()
    {
        return $this->getAttribute("tags");
    }

    public function getCreateTime()
    {
        return $this->getAttribute("createTime");
    }

    public function isVerify()
    {
        return $this->getAttribute("verify");
    }

}
