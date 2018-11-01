<?php

namespace Xxtime\Media\Message;


abstract class MessageAbstract implements MessageInterface
{


    private $data;


    public function __construct(array $data)
    {
        $this->data = $data;
    }


    final protected function getAttribute($name = "")
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }


    final public function getData()
    {
        return $this->data;
    }


    final public function getJsonData()
    {
        return json_encode($this->data);
    }

}
