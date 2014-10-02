<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\UserFacade;
use PHPOrchestra\UserBundle\Document\User;

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

        $facade->username = $mixed->getUsername();
        $facade->roles = implode(',', $mixed->getRoles());

        $facade->addLink('_self', $this->generateRoute(
            'php_orchestra_api_user_show',
            array('userId' => $mixed->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'php_orchestra_user_user_form',
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
