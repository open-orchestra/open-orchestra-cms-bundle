<?php

namespace PHPOrchestra\UserBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use PHPOrchestra\ModelBundle\Document\Role;

/**
 * Class LoadRoleData
 */
class LoadRoleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $admin = new Role();
        $admin->setName('ROLE_ADMIN');
        $manager->persist($admin);

        $user = new Role();
        $user->setName('ROLE_USER');
        $manager->persist($user);

        $draft = new Role();
        $draft->setName('ROLE_FROM_DRAFT_TO_PENDING');
        $draft->setFromStatus($this->getReference('status-draft'));
        $draft->setToStatus($this->getReference('status-pending'));
        $manager->persist($draft);

        $pending = new Role();
        $pending->setName('ROLE_FROM_PENDING_TO_PUBLISHED');
        $pending->setFromStatus($this->getReference('status-pending'));
        $pending->setToStatus($this->getReference('status-published'));
        $manager->persist($pending);

        $published = new Role();
        $published->setName('ROLE_FROM_PUBLISHED_TO_DRAFT');
        $published->setFromStatus($this->getReference('status-published'));
        $published->setToStatus($this->getReference('status-draft'));
        $manager->persist($published);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 55;
    }

}
