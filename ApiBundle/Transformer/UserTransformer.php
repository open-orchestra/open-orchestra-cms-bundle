<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Facade\UserFacade;
use OpenOrchestra\UserBundle\Document\User;

/**
 * Class UserTransformer
 */
class UserTransformer extends AbstractTransformer
{
    /**
     * @param User $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new UserFacade();

        $facade->id = $mixed->getId();
        $facade->username = $mixed->getUsername();
        $facade->roles = implode(',', $mixed->getRoles());

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_user_show',
            array('userId' => $mixed->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'open_orchestra_user_user_form',
            array('userId' => $mixed->getId())
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user';
    }

}
