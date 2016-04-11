<?php

namespace OpenOrchestra\GroupBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\ContentTypeForContentPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TransverseNodePanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeTemplatePanelStrategy;
use OpenOrchestra\GroupBundle\Document\Group;
use OpenOrchestra\ModelBundle\Document\TranslatedValue;

/**
 * Class AbstractLoadGroupData
 */
abstract class AbstractLoadGroupData extends AbstractFixture implements OrderedFixtureInterface
{
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
            $group->addRole(TransverseNodePanelStrategy::ROLE_ACCESS_TREE_GENERAL_NODE);
            $group->addRole(TransverseNodePanelStrategy::ROLE_ACCESS_UPDATE_GENERAL_NODE);
            $group->addRole(TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE);
            $group->addRole(TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE);
            $group->addRole(TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE);
            $group->addRole(TreeNodesPanelStrategy::ROLE_ACCESS_DELETE_NODE);
            $group->addRole(TreeNodesPanelStrategy::ROLE_ACCESS_MOVE_TREE);
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
