<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\RoleFacade;
use PHPOrchestra\ModelInterface\Model\RoleInterface;

/**
 * Class RoleTransformer
 */
class RoleTransformer extends AbstractTransformer
{
    /**
     * @param RoleInterface $mixed
     *
     * @return RoleFacade
     */
    public function transform($mixed)
    {
        $facade = new RoleFacade();

        $facade->id = $mixed->getId();
        $facade->name = $mixed->getName();
        $facade->fromStatus = $mixed->getFromStatus();
        $facade->toStatus = $mixed->getToStatus();

        $facade->addLink('_self', $this->generateRoute(
            'php_orchestra_api_role_show',
            array('roleId' => $mixed->getId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'php_orchestra_api_role_delete',
            array('roleId' => $mixed->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'php_orchestra_backoffice_role_form',
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
