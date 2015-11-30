<?php

namespace OpenOrchestra\GroupBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\ContentTypeForContentPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\GeneralNodesPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeTemplatePanelStrategy;
use OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface;
use OpenOrchestra\GroupBundle\Document\Group;
use OpenOrchestra\GroupBundle\Document\NodeGroupRole;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;
use OpenOrchestra\ModelBundle\Document\TranslatedValue;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class LoadGroupData
 */
class LoadGroupData extends AbstractFixture implements OrderedFixtureInterface, OrchestraProductionFixturesInterface, OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $group2 = $this->generateGroup('Demo group', 'Demo group', 'Groupe de démo', 'site2', 'group2');
        $group2->addRole(AdministrationPanelStrategy::ROLE_ACCESS_REDIRECTION);
        $manager->persist($group2);

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
     * Generate a translatedValue
     *
     * @param string $language
     * @param string $value
     *
     * @return TranslatedValue
     */
    protected function generateTranslatedValue($language, $value)
    {
        $label = new TranslatedValue();
        $label->setLanguage($language);
        $label->setValue($value);

        return $label;
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

    /**
     * @param string $name
     * @param string $enLabel
     * @param string $frLabel
     * @param string $siteNumber
     * @param string $referenceName
     * @param string $role
     *
     * @return Group
     */
    protected function generateGroup($name, $enLabel, $frLabel, $siteNumber, $referenceName, $role = null)
    {
        $group = new Group();
        $group->setName($name);

        $enLabel = $this->generateTranslatedValue('en', $enLabel);
        $frLabel = $this->generateTranslatedValue('fr', $frLabel);
        $group->addLabel($enLabel);
        $group->addLabel($frLabel);

        if (is_null($role)) {
            $group->addRole('ROLE_ADMIN');
            $group->addRole('ROLE_FROM_DRAFT_TO_PENDING');
            $group->addRole('ROLE_FROM_PENDING_TO_PUBLISHED');
            $group->addRole('ROLE_FROM_PUBLISHED_TO_DRAFT');
            $group->addRole(GeneralNodesPanelStrategy::ROLE_ACCESS_TREE_GENERAL_NODE);
            $group->addRole(GeneralNodesPanelStrategy::ROLE_ACCESS_UPDATE_GENERAL_NODE);
            $group->addRole(TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE);
            $group->addRole(TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE);
            $group->addRole(TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE);
            $group->addRole(TreeNodesPanelStrategy::ROLE_ACCESS_DELETE_NODE);
            $group->addRole(TreeNodesPanelStrategy::ROLE_ACCESS_MOVE_NODE);
            $group->addRole(TreeTemplatePanelStrategy::ROLE_ACCESS_TREE_TEMPLATE);
            $group->addRole(TreeTemplatePanelStrategy::ROLE_ACCESS_CREATE_TEMPLATE);
            $group->addRole(TreeTemplatePanelStrategy::ROLE_ACCESS_UPDATE_TEMPLATE);
            $group->addRole(TreeTemplatePanelStrategy::ROLE_ACCESS_DELETE_TEMPLATE);
            $group->addRole(ContentTypeForContentPanelStrategy::ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT);
            $group->addRole(ContentTypeForContentPanelStrategy::ROLE_ACCESS_CREATE_CONTENT_TYPE_FOR_CONTENT);
            $group->addRole(ContentTypeForContentPanelStrategy::ROLE_ACCESS_UPDATE_CONTENT_TYPE_FOR_CONTENT);
            $group->addRole(ContentTypeForContentPanelStrategy::ROLE_ACCESS_DELETE_CONTENT_TYPE_FOR_CONTENT);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_CONTENT_TYPE);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_CONTENT_TYPE);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_CONTENT_TYPE);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_CONTENT_TYPE);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_API_CLIENT);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_API_CLIENT);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_API_CLIENT);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_API_CLIENT);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_DELETED);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_RESTORE);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_KEYWORD);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_KEYWORD);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_KEYWORD);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_KEYWORD);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_STATUS);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_STATUS);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_STATUS);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_STATUS);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_GROUP);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_GROUP);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_GROUP);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_GROUP);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_USER);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_USER);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_USER);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_USER);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_SITE);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_SITE);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_SITE);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_SITE);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_ROLE);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_ROLE);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_ROLE);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_ROLE);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_REDIRECTION);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_REDIRECTION);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_REDIRECTION);
            $group->addRole(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_REDIRECTION);
        } else {
            $group->addRole($role);
        }

        $group->setSite($this->getReference($siteNumber));
        $this->setReference($referenceName, $group);

        return $group;
    }
}
