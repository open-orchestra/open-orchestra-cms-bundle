<?php

namespace OpenOrchestra\GroupBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;
use OpenOrchestra\GroupBundle\Document\Group;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;

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
        $sitePerimeter = $this->createPerimeter(SiteInterface::ENTITY_TYPE, array(
            $this->getReference('site2')->getSiteId()
        ));
        $sadmin2 = new Group('Site Admin demo');
        $sadmin2->addLabel('en', 'Site admin demo');
        $sadmin2->addLabel('fr', 'Admin site demo');
        $sadmin2->setSite($this->getReference('site2'));
        $sadmin2->addPerimeter($sitePerimeter);
        $this->addReference('s-admin-demo', $sadmin2);
        $manager->persist($sadmin2);

        $nodePerimeter = $this->createPerimeter(NodeInterface::ENTITY_TYPE, array(
            'root/fixture_page_legal_mentions',
            'root/fixture_page_contact'
        ));
        $nodeProfileCollection = $this->createProfileCollection(array('profile-Contributor'));

        $group = new Group('Group v2');
        $group->addLabel('en', 'Group v2');
        $group->addLabel('fr', 'Groupe v2');
        $group->setSite($this->getReference('site2'));
        $group->addWorkflowProfileCollection(NodeInterface::ENTITY_TYPE, $nodeProfileCollection);
        $group->addPerimeter($nodePerimeter);

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
