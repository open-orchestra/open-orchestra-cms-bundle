<?php

namespace OpenOrchestra\UserAdminBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BackofficeBundle\DataFixtures\MongoDB\AbstractLoadRoleData;

/**
 * Class LoadRoleData
 */
class LoadRoleData extends AbstractLoadRoleData
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
