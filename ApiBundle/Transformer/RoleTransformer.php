<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\RoleFacade;
use PHPOrchestra\UserBundle\Document\Role;

/**
 * Class RoleTransformer
 */
class RoleTransformer extends AbstractTransformer
{
    /**
     * @param Role $mixed
     *
     * @return RoleFacade
     */
    public function transform($mixed)
    {
        $facade = new RoleFacade();

        $facade->name = $mixed->getName();

        $facade->addLink('_self', $this->generateRoute(
            'php_orchestra_api_role_show',
            array('roleId' => $mixed->getId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'php_orchestra_api_role_delete',
            array('roleId' => $mixed->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'php_orchestra_user_role_form',
            array('roleId' => $mixed->getId())
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'role';
    }
}
