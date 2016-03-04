<?php

namespace OpenOrchestra\GroupBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;

/**
 * Class LoadGroupData
 */
class LoadGroupData extends AbstractLoadGroupData implements OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        $group3 = $this->generateGroup('Empty group', 'Empty group', 'Groupe vide', 'site3', 'group3');
        $group3->addRole(AdministrationPanelStrategy::ROLE_ACCESS_THEME);
        $group3->addRole(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_THEME);
        $group3->addRole(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_THEME);
        $group3->addRole(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_THEME);

        $manager->persist($group3);

        $groupContentType = $this->generateGroup('Content type group', 'Content type group', 'Groupe pour les types de contenu', 'site2', 'groupContentType', AdministrationPanelStrategy::ROLE_ACCESS_CONTENT_TYPE);
        $manager->persist($groupContentType);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 600;
    }
}
