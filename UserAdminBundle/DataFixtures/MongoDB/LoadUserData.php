<?php

namespace OpenOrchestra\UserAdminBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;

/**
 * Class LoadUserData
 */
class LoadUserData extends AbstractLoadUserData implements OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $demoUser = $this->generate('demo', 'group2');
        $this->addReference('user-demo', $demoUser);
        $demoUser->addGroup($this->getReference('group3'));
        $manager->persist($demoUser);

        $user1 = $this->generate('user1', 'group2');
        $this->addReference('user-user1', $user1);
        $manager->persist($user1);

        $userContentType = $this->generate('userContentType', 'groupContentType');
        $this->addReference('user-userContentType', $userContentType);
        $manager->persist($userContentType);

        $userLog = $this->generate('userLog', 'groupLog');
        $this->addReference('user-userLog', $userLog);
        $manager->persist($userLog);

        $userNoAccess = $this->generate('userNoAccess');
        $manager->persist($userNoAccess);

        $manager->flush();
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
