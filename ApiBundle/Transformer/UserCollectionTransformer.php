<?php

namespace PHPOrchestra\ApiBundle\Transformer;
use Doctrine\Common\Collections\Collection;
use PHPOrchestra\ApiBundle\Facade\UserCollectionFacade;


/**
 * Class UserCollectionTransformer
 */
class UserCollectionTransformer extends AbstractTransformer
{
    /**
     * @param Collection $mixed
     *
     * @return \PHPOrchestra\ApiBundle\Facade\FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new UserCollectionFacade();

        foreach ($mixed as $user) {
            $facade->addUser($this->getTransformer('user')->transform($user));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user_collection';
    }
}
