<?php

namespace OpenOrchestra\MediaAdminBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\MediaAdminBundle\LeftPanel\Strategies\TreeFolderPanelStrategy;
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
        $manager->persist($this->generateRole(TreeFolderPanelStrategy::ROLE_ACCESS_TREE_FOLDER));

        $manager->flush();
    }
}
