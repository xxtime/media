<?php


namespace Xxtime\Media;

use Xxtime\Media\ProviderInterface;


/**
 * @method array getCookies()
 * @method array setCookies($string)
 * @method array login()
 * @method array password($password, $pass)
 * @method \Xxtime\Media\Message\ResponsePost post(array $data)
 * @method array follow()
 * @method array unfollow()
 * @method \Xxtime\Media\Message\ResponseProfile getProfile(string $uid)
 * @method object getPosts(array $data)
 * @method object getFollowing(array $data)
 * @method object getFollowers(array $data)
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
