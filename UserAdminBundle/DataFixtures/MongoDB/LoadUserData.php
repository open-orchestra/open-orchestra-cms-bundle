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
        $developer = $this->generate('developer');
        $developer->addRole(ContributionRoleInterface::DEVELOPER);
        $manager->persist($developer);

        $padmin = $this->generate('p-admin');
        $padmin->addRole(ContributionRoleInterface::PLATFORM_ADMIN);
        $this->addReference('p-admin', $padmin);
        $manager->persist($padmin);

        $sadmin = $this->generate('s-admin', 's-admin-demo');
        $sadmin->addRole(ContributionRoleInterface::SITE_ADMIN);
        $manager->persist($sadmin);

        $demoUser = $this->generate('demo', 'group2');
        $this->addReference('user-demo', $demoUser);
        $demoUser->addGroup($this->getReference('group3'));
        $manager->persist($demoUser);

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
