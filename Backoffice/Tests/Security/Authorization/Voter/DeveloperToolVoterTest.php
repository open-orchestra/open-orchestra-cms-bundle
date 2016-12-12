<?php

namespace OpenOrchestra\Backoffice\Tests\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Model\RoleInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use OpenOrchestra\Backoffice\Security\Authorization\Voter\DeveloperToolVoter;
use OpenOrchestra\BaseApi\Model\ApiClientInterface;
use OpenOrchestra\LogBundle\Model\LogInterface;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use OpenOrchestra\ModelInterface\Model\RedirectionInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\Backoffice\Model\GroupInterface;

/**
 * Class DeveloperToolVoterTest
 */
class DeveloperToolVoterTest extends AbstractVoterTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();

        $this->voter = new DeveloperToolVoter();
    }

    /**
     * @return array
     */
    protected function getNotSupportedSubjects()
    {
        $node = $this->createPhakeNode();
        $content = $this->createPhakeContent();
        $trashItem = $this->createPhakeTrashItem();
        $site = $this->createPhakeSite();
        $redirection = $this->createPhakeRedirection();
        $log = $this->createPhakeLog();
        $user = $this->createPhakeUser();
        $group = $this->createPhakeGroup();
        $keyword = $this->createPhakeKeyword();
        $client = $this->createPhakeApiClient();

        return array(
            'Bad subject : Node'                    => array($node,                             ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Content'                 => array($content,                          ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Trash Item'              => array($trashItem,                        ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Site'                    => array($site,                             ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Redirection'             => array($redirection,                      ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Log'                     => array($log,                              ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : User'                    => array($user,                             ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Group'                   => array($group,                            ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Keyword'                 => array($keyword,                          ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Api client'              => array($client,                           ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Entity type Site'        => array(SiteInterface::ENTITY_TYPE,        ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Entity type Redirection' => array(RedirectionInterface::ENTITY_TYPE, ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Entity type User'        => array(UserInterface::ENTITY_TYPE,        ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Entity type Group'       => array(GroupInterface::ENTITY_TYPE,       ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Entity type Log'         => array(LogInterface::ENTITY_TYPE,         ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Entity type Keyword'     => array(KeywordInterface::ENTITY_TYPE,     ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Entity type Api client'  => array(ApiClientInterface::ENTITY_TYPE,   ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
        );
    }

    /**
     * @return array
     */
    protected function getNotSupportedAttributes()
    {
        $profile = $this->createPhakeWorkflowProfile();

        return array(
            'Bad action : Trash Purge'   => array($profile, ContributionActionInterface::TRASH_PURGE,   array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad action : Trash Restore' => array($profile, ContributionActionInterface::TRASH_RESTORE, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
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
        $actions = array(
            'Read'   => ContributionActionInterface::READ,
            'Edit'   => ContributionActionInterface::EDIT,
            'Delete' => ContributionActionInterface::DELETE,
        );

        $subjects = array(
            'content type'              => $this->createPhakeContentType(),
            'workflow profile'          => $this->createPhakeWorkflowProfile(),
            'status'                    => $this->createPhakeStatus(),
            'entity type content type'  => ContentTypeInterface::ENTITY_TYPE,
            'entity type workflow'      => WorkflowProfileInterface::ENTITY_TYPE,
            'entity type role'          => RoleInterface::ENTITY_TYPE,
            'entity type status'        => StatusInterface::ENTITY_TYPE,
        );

        $roles = array(
            'None' => array(),
            'Node contributor'        => array(ContributionRoleInterface::NODE_CONTRIBUTOR),
            'Node super editor'       => array(ContributionRoleInterface::NODE_SUPER_EDITOR),
            'Node super suppressor'    => array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR),
            'Content contributor'     => array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),
            'Content super editor'    => array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),
            'Content super suppressor' => array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR),
            'Trash restorer'          => array(ContributionRoleInterface::TRASH_RESTORER),
            'Trash suppressor'         => array(ContributionRoleInterface::TRASH_SUPRESSOR),
            'Site administrator'      => array(ContributionRoleInterface::SITE_ADMIN),
            'Platform administrator'  => array(ContributionRoleInterface::PLATFORM_ADMIN),
        );

        $badRoles = array();

        foreach ($actions as $label => $action) {
            $key1 = 'Bad role (' . $label;

            foreach ($subjects as $label => $subject) {
                $key2 = ' ' . $label . ') : ';

                foreach ($roles as $label => $role) {
                    $badRoles[$key1 . $key2 . $label] = array($subject, $action, $role, null, VoterInterface::ACCESS_DENIED);
                }
            }
        }

        return $badRoles;
    }

    /**
     * @return array
     */
    protected function getOkVotes()
    {
        $contentType = $this->createPhakeContentType();
        $profile = $this->createPhakeWorkflowProfile();
        $status = $this->createPhakeStatus();

        return array(
            'Ok : Read content type'               => array($contentType,                          ContributionActionInterface::READ,   array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Add content type'                => array($contentType,                          ContributionActionInterface::CREATE, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Edit content type'               => array($contentType,                          ContributionActionInterface::EDIT,   array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Delete content type'             => array($contentType,                          ContributionActionInterface::DELETE, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Read profile'                    => array($profile,                              ContributionActionInterface::READ,   array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Add profile'                     => array($profile,                              ContributionActionInterface::CREATE, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Edit profile'                    => array($profile,                              ContributionActionInterface::EDIT,   array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Delete profile'                  => array($profile,                              ContributionActionInterface::DELETE, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Read status'                     => array($status,                               ContributionActionInterface::READ,   array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Add status'                      => array($status,                               ContributionActionInterface::CREATE, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Edit status'                     => array($status,                               ContributionActionInterface::EDIT,   array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Delete status'                   => array($status,                               ContributionActionInterface::DELETE, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Delete entity type content type' => array(ContentTypeInterface::ENTITY_TYPE,     ContributionActionInterface::READ,   array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Read entity type workflow'       => array(WorkflowProfileInterface::ENTITY_TYPE, ContributionActionInterface::READ,   array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Read entity type role'           => array(RoleInterface::ENTITY_TYPE,            ContributionActionInterface::READ,   array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Read entity type status'         => array(StatusInterface::ENTITY_TYPE,          ContributionActionInterface::READ,   array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Create entity type content type' => array(ContentTypeInterface::ENTITY_TYPE,     ContributionActionInterface::CREATE, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Create entity type workflow'     => array(WorkflowProfileInterface::ENTITY_TYPE, ContributionActionInterface::CREATE, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Create entity type role'         => array(RoleInterface::ENTITY_TYPE,            ContributionActionInterface::CREATE, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Create entity type status'       => array(StatusInterface::ENTITY_TYPE,          ContributionActionInterface::CREATE, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_GRANTED),
        );
    }
}
