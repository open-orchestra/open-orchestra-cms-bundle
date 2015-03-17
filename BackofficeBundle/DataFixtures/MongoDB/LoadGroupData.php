<?php

namespace OpenOrchestra\BackofficeBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\BackofficeBundle\Document\Group;

/**
 * Class LoadGroupData
 */
class LoadGroupData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $manager->persist($this->generateGroup('Demo group', 'site2', 'group2'));
        $manager->persist($this->generateGroup('Echonext group', 'site3', 'group3'));

        $manager->flush();

    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 600;
    }

    /**
     * @param string $name
     * @param string $siteNumber
     * @param string $referenceName
     *
     * @return Group
     */
    protected function generateGroup($name, $siteNumber, $referenceName)
    {
        $group = new Group();
        $group->setName($name);
        $group->addRole('ROLE_ADMIN');
        $group->addRole('ROLE_FROM_DRAFT_TO_PENDING');
        $group->addRole('ROLE_FROM_PENDING_TO_PUBLISHED');
        $group->addRole('ROLE_FROM_PUBLISHED_TO_DRAFT');
        $group->setSite($this->getReference($siteNumber));
        $this->setReference($referenceName, $group);

        return $group;
    }

}
