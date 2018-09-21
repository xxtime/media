<?php


namespace Xxtime\Media;

use Xxtime\Media\Exception\ErrorException;


/**
 * @method array getCookies()
 * @method array setCookies()
 * @method array login()
 * @method array logout()
 * @method array password()
 * @method array post()
 * @method array follow()
 * @method array unfollow()
 * @method array visit()
 */
class MediaAdaptor
{

    private $adaptor;


    public function __construct($adaptor = null)
    {
        if (!$adaptor) {
            throw new ErrorException('no adaptor');
        }
        if (is_object($adaptor)) {
            $this->adaptor = $adaptor;
        }
        else {
            throw new ErrorException('error adaptor');
        }
    }


    public function __call($name, $arguments)
    {
        return $this->adaptor->$name($arguments);
    }

}
