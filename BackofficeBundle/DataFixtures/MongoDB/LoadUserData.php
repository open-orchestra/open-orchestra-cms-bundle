<?php

namespace OpenOrchestra\BackofficeBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\UserBundle\Document\User;

/**
 * Class LoadUserData
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $admin = $this->generate('admin', 'group2');
        $admin->addGroup($this->getReference('group3'));
        $admin->addGroup($this->getReference('group1'));
        $manager->persist($admin);

        $nicolas = $this->generate('nicolas', 'group2');
        $nicolas->addGroup($this->getReference('group3'));
        $nicolas->addGroup($this->getReference('group1'));
        $manager->persist($nicolas);

        $benjamin = $this->generate('benjamin', 'group2');
        $manager->persist($benjamin);

        $noel = $this->generate('noel', 'group3');
        $manager->persist($noel);

        $manager->flush();
    }

    /**
     * @param string $name
     * @param string $group
     *
     * @return User
     */
    protected function generate($name, $group)
    {
        $user = new User();

        $user->setFirstName($name);
        $user->setLastName($name);
        $user->setEmail($name.'@fixtures.com');
        $user->setUsername($name);
        $user->setPlainPassword($name);
        $user->addGroup($this->getReference($group));
        $user->setEnabled(true);

        return $user;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 700;
    }
}
