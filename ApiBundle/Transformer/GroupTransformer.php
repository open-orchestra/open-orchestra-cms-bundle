<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\GroupFacade;
use OpenOrchestra\BackofficeBundle\Model\GroupInterface;

/**
 * Class GroupTransformer
 */
class GroupTransformer extends AbstractTransformer
{
    /**
     * @param GroupInterface $mixed
     *
     * @return GroupFacade
     */
    public function transform($mixed)
    {
        $facade = new GroupFacade();

        $facade->id = $mixed->getId();
        $facade->name = $mixed->getName();
        foreach ($mixed->getRoles() as $role) {
            $facade->addRole($role);
        }
        $facade->site = $this->getTransformer('site')->transform($mixed->getSite());


        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_group_show',
            array('groupId' => $mixed->getId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'open_orchestra_api_group_delete',
            array('groupId' => $mixed->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'open_orchestra_backoffice_group_form',
            array('groupId' => $mixed->getId())
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'group';
    }
}
