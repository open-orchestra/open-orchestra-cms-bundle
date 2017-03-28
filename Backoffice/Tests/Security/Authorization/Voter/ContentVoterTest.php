<?php

namespace OpenOrchestra\Backoffice\Tests\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use OpenOrchestra\Backoffice\Security\Authorization\Voter\ContentVoter;
use OpenOrchestra\BaseApi\Model\ApiClientInterface;
use OpenOrchestra\LogBundle\Model\LogInterface;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use OpenOrchestra\ModelInterface\Model\RedirectionInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\Backoffice\Model\GroupInterface;

/**
 * Class ContentVoterTest
 */
class ContentVoterTest extends AbstractVoterTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();

        $this->voter = new ContentVoter($this->accessDecisionManager, $this->perimeterManager);
    }

    /**
     * @return array
     */
    protected function getNotSupportedSubjects()
    {
        $node = $this->createPhakeNode();
        $trashItem = $this->createPhakeTrashItem();
        $site = $this->createPhakeSite();
        $redirection = $this->createPhakeRedirection();
        $log = $this->createPhakeLog();
        $user = $this->createPhakeUser();
        $group = $this->createPhakeGroup();
        $keyword = $this->createPhakeKeyword();
        $client = $this->createPhakeApiClient();

        return array(
            'Bad subject : Node'                    => array($node,                                 ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Trash Item'              => array($trashItem,                            ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Site'                    => array($site,                                 ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Redirection'             => array($redirection,                          ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Log'                     => array($log,                                  ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : User'                    => array($user,                                 ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Group'                   => array($group,                                ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Keyword'                 => array($keyword,                              ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Api client'              => array($client,                               ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Entity type ContentType' => array(ContentTypeInterface::ENTITY_TYPE,     ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Entity type Workflow'    => array(WorkflowProfileInterface::ENTITY_TYPE, ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Entity type Status'      => array(StatusInterface::ENTITY_TYPE,          ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Entity type Site'        => array(SiteInterface::ENTITY_TYPE,            ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Entity type Redirection' => array(RedirectionInterface::ENTITY_TYPE,     ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Entity type User'        => array(UserInterface::ENTITY_TYPE,            ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Entity type Group'       => array(GroupInterface::ENTITY_TYPE,           ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Entity type Log'         => array(LogInterface::ENTITY_TYPE,             ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Entity type Keyword'     => array(KeywordInterface::ENTITY_TYPE,         ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Entity type Api client'  => array(ApiClientInterface::ENTITY_TYPE,       ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
        );
    }

    /**
     * @return array
     */
    protected function getNotSupportedAttributes()
    {
        $content = $this->createPhakeContent();

        return array(
            'Bad action : Trash Purge'   => array($content, ContributionActionInterface::TRASH_PURGE,   array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad action : Trash Restore' => array($content, ContributionActionInterface::TRASH_RESTORE, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
        );
    }

    /**
     * @return array
     */
    protected function getNotInPerimeter()
    {
        $content = $this->createPhakeContent();
        $contentSelf = $this->createPhakeContent(true);

        return array(
            'Not in perimeter : Edit self'    => array($contentSelf,                  ContributionActionInterface::EDIT,   array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Delete self'  => array($contentSelf,                  ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Edit other'   => array($content,                      ContributionActionInterface::EDIT,   array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),    false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Delete other' => array($content,                      ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : read other'   => array(ContentInterface::ENTITY_TYPE, ContributionActionInterface::READ,   array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : create other' => array(ContentInterface::ENTITY_TYPE, ContributionActionInterface::CREATE, array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), false, VoterInterface::ACCESS_DENIED),
        );
    }

    /**
     * @return array
     */
    protected function getBadRoles()
    {
        $content = $this->createPhakeContent();

        return array(
            'Bad role (Edit) : None'                              => array($content,                      ContributionActionInterface::EDIT,   array(),                                                true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Node contributor'                  => array($content,                      ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_CONTRIBUTOR),     true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Node super editor'                 => array($content,                      ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_SUPER_EDITOR),    true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Node super suppressor'             => array($content,                      ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR), true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Site Admin'                        => array($content,                      ContributionActionInterface::EDIT,   array(ContributionRoleInterface::SITE_ADMIN),           true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Trash Restorer'                    => array($content,                      ContributionActionInterface::EDIT,   array(ContributionRoleInterface::TRASH_RESTORER),       true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Trash Suppressor'                  => array($content,                      ContributionActionInterface::EDIT,   array(ContributionRoleInterface::TRASH_SUPRESSOR),      true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : None'                            => array($content,                      ContributionActionInterface::DELETE, array(),                                                true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Node contributor'                => array($content,                      ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_CONTRIBUTOR),     true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Node super editor'               => array($content,                      ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_SUPER_EDITOR),    true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Node super suppressor'           => array($content,                      ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR), true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Site Admin'                      => array($content,                      ContributionActionInterface::DELETE, array(ContributionRoleInterface::SITE_ADMIN),           true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Trash Restorer'                  => array($content,                      ContributionActionInterface::DELETE, array(ContributionRoleInterface::TRASH_RESTORER),       true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Trash Suppressor'                => array($content,                      ContributionActionInterface::DELETE, array(ContributionRoleInterface::TRASH_SUPRESSOR),      true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : entity type node contributor'      => array(ContentInterface::ENTITY_TYPE, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_CONTRIBUTOR),     true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : entity type node super editor'     => array(ContentInterface::ENTITY_TYPE, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_SUPER_EDITOR),    true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : entity type node super suppressor' => array(ContentInterface::ENTITY_TYPE, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR), true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : entity type Site Admin'            => array(ContentInterface::ENTITY_TYPE, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::SITE_ADMIN),           true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : entity type Trash Restore'         => array(ContentInterface::ENTITY_TYPE, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::TRASH_RESTORER),       true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : entity type Trash Suppressor'      => array(ContentInterface::ENTITY_TYPE, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::TRASH_SUPRESSOR),      true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : entity type nobe'                  => array(ContentInterface::ENTITY_TYPE, ContributionActionInterface::EDIT,   array(),                                                true, VoterInterface::ACCESS_DENIED),
        );
    }

    /**
     * @return array
     */
    protected function getOkVotes()
    {
        $content = $this->createPhakeContent();
        $contentSelf = $this->createPhakeContent(true);

        return array(
            'Ok : Read'               => array($content,                      ContributionActionInterface::READ,   array(),                                                   true, VoterInterface::ACCESS_GRANTED),
            'Ok : Add self'           => array($contentSelf,                  ContributionActionInterface::CREATE, array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     true, VoterInterface::ACCESS_GRANTED),
            'Ok : Edit self'          => array($contentSelf,                  ContributionActionInterface::EDIT,   array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     true, VoterInterface::ACCESS_GRANTED),
            'Ok : Delete self'        => array($contentSelf,                  ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     true, VoterInterface::ACCESS_GRANTED),
            'Ok : Edit other'         => array($content,                      ContributionActionInterface::EDIT,   array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),    true, VoterInterface::ACCESS_GRANTED),
            'Ok : Delete other'       => array($content,                      ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), true, VoterInterface::ACCESS_GRANTED),
            'Ok : Read entity type'   => array(ContentInterface::ENTITY_TYPE, ContributionActionInterface::READ,   array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     true, VoterInterface::ACCESS_GRANTED),
            'Ok : create entity type' => array($contentSelf,                  ContributionActionInterface::CREATE, array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     true, VoterInterface::ACCESS_GRANTED),
        );
    }
}
