<?php

namespace OpenOrchestra\GroupBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\ContentTypeForContentPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\GroupBundle\Document\Group;

/**
 * Class AbstractLoadGroupData
 */
abstract class AbstractLoadGroupData extends AbstractFixture implements OrderedFixtureInterface
{
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
    protected function generateGroup($name, $enLabel, $frLabel, $siteNumber = null, $referenceName = null, $role = null)
    {
        $group = new Group();
        $group->setName($name);

        $group->addLabel('en', $enLabel);
        $group->addLabel('fr', $frLabel);

        if (is_null($role)) {
            $group->addRole(TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE);
            $group->addRole(TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE);
            $group->addRole(TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE);
            $group->addRole(TreeNodesPanelStrategy::ROLE_ACCESS_DELETE_NODE);
            $group->addRole(TreeNodesPanelStrategy::ROLE_ACCESS_MOVE_TREE);
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

        if (!is_null($siteNumber)) {
            $group->setSite($this->getReference($siteNumber));
        }
        if (!is_null($referenceName)) {
            $this->setReference($referenceName, $group);
        }

        return $group;
    }
}
