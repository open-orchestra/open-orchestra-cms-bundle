<?php

namespace OpenOrchestra\Backoffice\Tests\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use OpenOrchestra\Backoffice\Security\Authorization\Voter\TrashItemVoter;
use OpenOrchestra\BaseApi\Model\ApiClientInterface;
use OpenOrchestra\LogBundle\Model\LogInterface;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use OpenOrchestra\ModelInterface\Model\RedirectionInterface;
use OpenOrchestra\ModelInterface\Model\RoleInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\Backoffice\Model\GroupInterface;


/**
 * Class TrashItemVoterTest
 */
class TrashItemVoterTest extends AbstractVoterTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();

        $this->voter = new TrashItemVoter();
    }

    /**
     * @return array
     */
    protected function getNotSupportedSubjects()
    {
        $node = $this->createPhakeNode();
        $content = $this->createPhakeContent();
        $site = $this->createPhakeSite();
        $redirection = $this->createPhakeRedirection();
        $log = $this->createPhakeLog();
        $user = $this->createPhakeUser();
        $group = $this->createPhakeGroup();
        $keyword = $this->createPhakeKeyword();
        $client = $this->createPhakeApiClient();
        $contentType = $this->createPhakeContentType();
        $profile = $this->createPhakeWorkflowProfile();
        $status = $this->createPhakeStatus();

        return array(
            'Bad subject : Node'                    => array($node,                                 ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Content'                 => array($content,                              ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Site'                    => array($site,                                 ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Redirection'             => array($redirection,                          ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Log'                     => array($log,                                  ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : User'                    => array($user,                                 ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Group'                   => array($group,                                ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Keyword'                 => array($keyword,                              ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Api client'              => array($client,                               ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Content type'            => array($contentType,                          ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Workflow profile'        => array($profile,                              ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Status'                  => array($status,                               ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Entity type ContentType' => array(ContentTypeInterface::ENTITY_TYPE,     ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Entity type Workflow'    => array(WorkflowProfileInterface::ENTITY_TYPE, ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Entity type Role'        => array(RoleInterface::ENTITY_TYPE,            ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
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
        $trashItem = $this->createPhakeTrashItem();

        return array(
            'Bad action : Add'    => array($trashItem, ContributionActionInterface::CREATE, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad action : Edit'   => array($trashItem, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad action : Delete' => array($trashItem, ContributionActionInterface::DELETE, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
        );
    }

    /**
     * @return array
     */
    protected function getNotInPerimeter()
    {
        return array();
    }

    /**
     * @return array
     */
    protected function getBadRoles()
    {
        $trashItem = $this->createPhakeTrashItem();

        return array(
            'Bad role (Restore) : None'                    => array($trashItem, ContributionActionInterface::TRASH_RESTORE, array(),                                                   null, VoterInterface::ACCESS_DENIED),
            'Bad role (Restore) : Node contributor'        => array($trashItem, ContributionActionInterface::TRASH_RESTORE, array(ContributionRoleInterface::NODE_CONTRIBUTOR),        null, VoterInterface::ACCESS_DENIED),
            'Bad role (Restore) : Node super editor'       => array($trashItem, ContributionActionInterface::TRASH_RESTORE, array(ContributionRoleInterface::NODE_SUPER_EDITOR),       null, VoterInterface::ACCESS_DENIED),
            'Bad role (Restore) : Node super supressor'    => array($trashItem, ContributionActionInterface::TRASH_RESTORE, array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Restore) : Content contributor'     => array($trashItem, ContributionActionInterface::TRASH_RESTORE, array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     null, VoterInterface::ACCESS_DENIED),
            'Bad role (Restore) : Content super editor'    => array($trashItem, ContributionActionInterface::TRASH_RESTORE, array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Restore) : Content super supressor' => array($trashItem, ContributionActionInterface::TRASH_RESTORE, array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), null, VoterInterface::ACCESS_DENIED),
            'Bad role (Restore) : Site Admin'              => array($trashItem, ContributionActionInterface::TRASH_RESTORE, array(ContributionRoleInterface::SITE_ADMIN),              null, VoterInterface::ACCESS_DENIED),
            'Bad role (Purge) : None'                      => array($trashItem, ContributionActionInterface::TRASH_PURGE,   array(),                                                   null, VoterInterface::ACCESS_DENIED),
            'Bad role (Purge) : Node contributor'          => array($trashItem, ContributionActionInterface::TRASH_PURGE,   array(ContributionRoleInterface::NODE_CONTRIBUTOR),        null, VoterInterface::ACCESS_DENIED),
            'Bad role (Purge) : Node super editor'         => array($trashItem, ContributionActionInterface::TRASH_PURGE,   array(ContributionRoleInterface::NODE_SUPER_EDITOR),       null, VoterInterface::ACCESS_DENIED),
            'Bad role (Purge) : Node super supressor'      => array($trashItem, ContributionActionInterface::TRASH_PURGE,   array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Purge) : Content contributor'       => array($trashItem, ContributionActionInterface::TRASH_PURGE,   array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     null, VoterInterface::ACCESS_DENIED),
            'Bad role (Purge) : Content super editor'      => array($trashItem, ContributionActionInterface::TRASH_PURGE,   array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Purge) : Content super supressor'   => array($trashItem, ContributionActionInterface::TRASH_PURGE,   array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), null, VoterInterface::ACCESS_DENIED),
            'Bad role (Purge) : Site Admin'                => array($trashItem, ContributionActionInterface::TRASH_PURGE,   array(ContributionRoleInterface::SITE_ADMIN),              null, VoterInterface::ACCESS_DENIED),
        );
    }

    /**
     * @return array
     */
    protected function getOkVotes()
    {
        $trashItem = $this->createPhakeTrashItem();

        return array(
            'Ok : Read'    => array($trashItem, ContributionActionInterface::READ,          array(),                                           null, VoterInterface::ACCESS_GRANTED),
            'Ok : Restore' => array($trashItem, ContributionActionInterface::TRASH_RESTORE, array(ContributionRoleInterface::TRASH_RESTORER),  null, VoterInterface::ACCESS_GRANTED),
            'Ok : Purge'   => array($trashItem, ContributionActionInterface::TRASH_PURGE,   array(ContributionRoleInterface::TRASH_SUPRESSOR), null, VoterInterface::ACCESS_GRANTED),
        );
    }
}
