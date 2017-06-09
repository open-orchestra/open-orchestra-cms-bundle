<?php

namespace OpenOrchestra\GroupBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;
use OpenOrchestra\GroupBundle\Document\Group;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;

/**
 * Class LoadGroupData
 */
class LoadGroupData extends AbstractLoadGroupData implements OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $sadmin2 = $this->createSiteAdminGroup();
        $this->addReference('s-admin-demo', $sadmin2);
        $manager->persist($sadmin2);

        $group = $this->createEmptyGroup();
        $this->addReference('group3', $group);
        $manager->persist($group);

        $groupDemo = $this->createDemoGroup();
        $this->addReference('group2', $groupDemo);
        $manager->persist($groupDemo);

        $manager->flush();
    }

    /**
     * Create Site 2 Admin group
     *
     * @return \OpenOrchestra\GroupBundle\Document\Group
     */
    protected function createSiteAdminGroup()
    {
        $sitePerimeter = $this->createPerimeter(SiteInterface::ENTITY_TYPE, array(
            $this->getReference('site2')->getSiteId()
        ));

        $sadmin2 = new Group();
        $sadmin2->addLabel('en', 'Site admin demo');
        $sadmin2->addLabel('fr', 'Admin site demo');
        $sadmin2->setSite($this->getReference('site2'));
        $sadmin2->addRole(ContributionRoleInterface::SITE_ADMIN);
        $sadmin2->addPerimeter($sitePerimeter);

        return $sadmin2;
    }

    /**
     * Create group V2
     *
     * @return \OpenOrchestra\GroupBundle\Document\Group
     */
    protected function createDemoGroup()
    {
        $nodePerimeter = $this->createPerimeter(NodeInterface::ENTITY_TYPE, array(
            'root/fixture_page_legal_mentions',
            'root/fixture_page_contact'
        ));
        $nodeProfileCollection = $this->createProfileCollection(array('profile-Contributor'));

        $group = new Group();
        $group->addLabel('en', 'Demo group');
        $group->addLabel('fr', 'Groupe de démo');
        $group->setSite($this->getReference('site2'));
        $group->addWorkflowProfileCollection(NodeInterface::ENTITY_TYPE, $nodeProfileCollection);
        $group->addPerimeter($nodePerimeter);

        return $group;
    }

    protected function createEmptyGroup()
    {
        $group = new Group();
        $group->addLabel('en', 'Empty group');
        $group->addLabel('fr', 'Groupe vide');
        $group->setSite($this->getReference('site3'));

        return $group;
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
