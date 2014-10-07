<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\RoleCollectionFacade;

/**
 * Class RoleCollectionTransformer
 */
class RoleCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new RoleCollectionFacade();

        foreach ($mixed as $role) {
            $facade->addRole($this->getTransformer('role')->transform($role));
        }

        $facade->addLink('_self', $this->generateRoute(
            'php_orchestra_api_role_list',
            array()
        ));

        $facade->addLink('_self_add', $this->generateRoute(
            'php_orchestra_user_role_new',
            array()
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'role_collection';
    }
}
