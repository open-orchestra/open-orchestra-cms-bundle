<?php

namespace OpenOrchestra\ApiBundle\Transformer;
use Doctrine\Common\Collections\Collection;
use OpenOrchestra\ApiBundle\Facade\UserCollectionFacade;


/**
 * Class UserCollectionTransformer
 */
class UserCollectionTransformer extends AbstractTransformer
{
    /**
     * @param Collection $mixed
     *
     * @return \OpenOrchestra\ApiBundle\Facade\FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new UserCollectionFacade();

        foreach ($mixed as $user) {
            $facade->addUser($this->getTransformer('user')->transform($user));
        }

        $facade->addLink('_self_add', $this->generateRoute('open_orchestra_user_new'));

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
