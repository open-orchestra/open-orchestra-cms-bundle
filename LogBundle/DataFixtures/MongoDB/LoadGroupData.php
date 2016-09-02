<?php

namespace OpenOrchestra\LogBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\GroupBundle\Document\Group;
use OpenOrchestra\LogBundle\NavigationPanel\Strategies\LogPanelStrategy;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;

/**
 * Class LoadUserData
 */
class LoadGroupData extends AbstractFixture implements OrderedFixtureInterface, OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $groupLog = $this->generateGroup('Log group', 'Log group', 'Groupe de consultation de log', 'site2', 'groupLog', LogPanelStrategy::ROLE_ACCESS_LOG);
        $manager->persist($groupLog);

        $manager->flush();
    }

    /**
     * @param string $name
     * @param string $enLabel
     * @param string $frLabel
     * @param string $siteNumber
     * @param string $referenceName
     * @param string $role
     *
     * @return Group
     */
    protected function generateGroup($name, $enLabel, $frLabel, $siteNumber, $referenceName, $role = null)
    {
        $group = new Group();
        $group->setName($name);

        $group->addLabel('en', $enLabel);
        $group->addLabel('fr', $frLabel);

        if (!is_null($role)) {
            $group->addRole($role);
        }

        $group->setSite($this->getReference($siteNumber));
        $this->setReference($referenceName, $group);

        return $group;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 602;
    }
}
