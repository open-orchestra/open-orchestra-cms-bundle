<?php

namespace OpenOrchestra\UserAdminBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface;

/**
 * Class LoadUserAdminData
 */
class LoadUserAdminData extends AbstractLoadUserData implements OrchestraFunctionalFixturesInterface, OrchestraProductionFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $admin = $this->generate('admin', 'group2');
        $this->addReference('user-admin', $admin);
        $admin->setSuperAdmin(true);
        $manager->persist($admin);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 710;
    }
}
