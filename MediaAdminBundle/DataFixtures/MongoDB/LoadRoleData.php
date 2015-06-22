<?php

namespace OpenOrchestra\MediaAdminBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\BackofficeBundle\DataFixtures\MongoDB\AbstractLoadRoleData;
use OpenOrchestra\MediaAdminBundle\LeftPanel\Strategies\TreeFolderPanelStrategy;

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
        $manager->persist($this->generateRole(TreeFolderPanelStrategy::ROLE_ACCESS_TREE_FOLDER));

        $manager->flush();
    }
}
