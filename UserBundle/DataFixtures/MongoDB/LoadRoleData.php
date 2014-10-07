<?php

namespace PHPOrchestra\UserBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use PHPOrchestra\UserBundle\Document\Role;

/**
 * Class LoadRoleData
 */
class LoadRoleData implements FixtureInterface
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
        $draft->setName('ROLE_FROM_DRAFT');
        $manager->persist($draft);

        $pending = new Role();
        $pending->setName('ROLE_FROM_PENDING');
        $manager->persist($pending);

        $toPending = new Role();
        $toPending->setName('ROLE_TO_PENDING');
        $manager->persist($toPending);

        $published = new Role();
        $published->setName('ROLE_TO_PUBLISHED');
        $manager->persist($published);

        $manager->flush();
    }

}
