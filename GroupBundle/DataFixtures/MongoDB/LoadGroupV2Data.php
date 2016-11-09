<?php

namespace OpenOrchestra\GroupBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;
use OpenOrchestra\GroupBundle\Document\Group;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class LoadGroupV2Data
 */
class LoadGroupV2Data extends AbstractLoadGroupV2Data implements OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $nodePerimeter = $this->createPerimeter(array(
            'root/fixture_page_legal_mentions',
            'root/fixture_page_contact'
        ));
        $nodeProfileCollection = $this->createProfileCollection(array('profile-Contributor'));

        $group = new Group('Group v2');
        $group->addLabel('en', 'Group v2');
        $group->addLabel('fr', 'Groupe v2');
        $group->setSite($this->getReference('site2'));
        $group->addWorkflowProfileCollection(NodeInterface::ENTITY_TYPE, $nodeProfileCollection);
        $group->addPerimeter(NodeInterface::ENTITY_TYPE, $nodePerimeter);

        $this->addReference('group-v2', $group);

        $manager->persist($group);
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 612;
    }
}
