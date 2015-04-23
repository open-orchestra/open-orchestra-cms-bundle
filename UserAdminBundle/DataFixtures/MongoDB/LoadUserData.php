<?php

namespace OpenOrchestra\UserAdminBundle\DataFixtures\MongoDB;

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
        $this->addReference('user-admin', $admin);
        $admin->addGroup($this->getReference('group3'));
        $admin->addGroup($this->getReference('group1'));
        $manager->persist($admin);

        $user1 = $this->generate('user1', 'group1');
        $this->addReference('user-user1', $user1);
        $manager->persist($user1);
        $userContentType = $this->generate('userContentType', 'groupContentType');
        $this->addReference('user-userContentType', $userContentType);
        $manager->persist($userContentType);
        $userLog = $this->generate('userLog', 'groupLog');
        $this->addReference('usre-userLog', $userLog);
        $manager->persist($userLog);

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
