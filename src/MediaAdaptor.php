<?php


namespace Xxtime\Media;

use Xxtime\Media\ProviderInterface;


/**
 * @method array getCookies()
 * @method array setCookies($string)
 * @method array login()
 * @method array logout()
 * @method array password($password, $pass)
 * @method array post($text, $att)
 * @method array follow()
 * @method array unfollow()
 * @method array visit()
 */
class MediaAdaptor
{

    private $adaptor;


    public function __construct(ProviderInterface $adaptor)
    {
        $this->adaptor = $adaptor;
    }


    public function __call($name, $arguments)
    {
        return $this->adaptor->$name(...$arguments);
    }

}
