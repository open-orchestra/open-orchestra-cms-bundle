<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\WorkflowFunctionAdminBundle\LeftPanel\Strategies\WorkflowFunctionPanelStrategy;

/**
 * Class LoadGroupData
 */
class LoadGroupData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach (array('group1', 'group2', 'group3') as $groupName) {
            $group = $this->getReference($groupName);
            $group->addRole(WorkflowFunctionPanelStrategy::ROLE_ACCESS_WORKFLOWFUNCTION);
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 650;
    }

}
