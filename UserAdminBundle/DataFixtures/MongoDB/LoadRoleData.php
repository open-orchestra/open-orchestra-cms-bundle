<?php

namespace OpenOrchestra\UserAdminBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\LeftPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BackofficeBundle\DataFixtures\MongoDB\LoadRoleData as BaseLoadRoleData;

/**
 * Class LoadRoleData
 */
class LoadRoleData extends BaseLoadRoleData
{
    /**
     * @param ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $manager->persist($this->generateRole(AdministrationPanelStrategy::ROLE_ACCESS_USER));

        $manager->flush();
    }
}
