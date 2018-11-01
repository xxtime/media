<?php

namespace Xxtime\Media\Message;


class ResponsePost extends MessageAbstract
{

    public function __construct(array $data)
    {
        parent::__construct($data);
    }

    public function getId()
    {
        return $this->getAttribute("id");
    }

    public function getUrl()
    {
        return $this->getAttribute("url");
    }

    public function getImages()
    {
        return $this->getAttribute("images");
    }

    public function getTitle()
    {
        return $this->getAttribute("title");
    }

    public function getContent()
    {
        return $this->getAttribute("content");
    }

}
