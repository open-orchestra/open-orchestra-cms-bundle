<?php

namespace OpenOrchestra\UserAdminBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;

/**
 * Class LoadUserV2Data
 */
class LoadUserV2Data extends AbstractLoadUserData implements OrchestraFunctionalFixturesInterface
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

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 701;
    }
}
