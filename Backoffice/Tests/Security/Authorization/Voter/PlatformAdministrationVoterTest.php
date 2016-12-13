<?php

namespace OpenOrchestra\Backoffice\Tests\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use OpenOrchestra\BaseApi\Model\ApiClientInterface;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use OpenOrchestra\Backoffice\Security\Authorization\Voter\PlatformAdministrationVoter;
use OpenOrchestra\LogBundle\Model\LogInterface;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Model\RedirectionInterface;
use OpenOrchestra\ModelInterface\Model\RoleInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\Backoffice\Model\GroupInterface;

/**
 * Class PlatformAdministrationVoterTest
 */
class PlatformAdministrationVoterTest extends AbstractVoterTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();

        $this->voter = new PlatformAdministrationVoter($this->accessDecisionManager);
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
        $contentType = $this->createPhakeContentType();
        $profile = $this->createPhakeWorkflowProfile();
        $status = $this->createPhakeStatus();

        return array(
            'Bad subject : Node'                    => array($node,                                 ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Content'                 => array($content,                              ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Trash Item'              => array($trashItem,                            ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Site'                    => array($site,                                 ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Redirection'             => array($redirection,                          ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Log'                     => array($log,                                  ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : User'                    => array($user,                                 ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Group'                   => array($group,                                ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
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
        );
    }

    /**
     * @return array
     */
    protected function getNotSupportedAttributes()
    {
        $keyword = $this->createPhakeKeyword();

        return array(
            'Bad action : Trash Purge'   => array($keyword, ContributionActionInterface::TRASH_PURGE,   array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad action : Trash Restore' => array($keyword, ContributionActionInterface::TRASH_RESTORE, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
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
        $keyword = $this->createPhakeKeyword();

        return array(
            'Bad role (Add) : None'                                             => array($keyword,                      ContributionActionInterface::CREATE, array(),                                                   null, VoterInterface::ACCESS_DENIED),
            'Bad role (Add) : Node contributor'                                 => array($keyword,                      ContributionActionInterface::CREATE, array(ContributionRoleInterface::NODE_CONTRIBUTOR),        null, VoterInterface::ACCESS_DENIED),
            'Bad role (Add) : Node super editor'                                => array($keyword,                      ContributionActionInterface::CREATE, array(ContributionRoleInterface::NODE_SUPER_EDITOR),       null, VoterInterface::ACCESS_DENIED),
            'Bad role (Add) : Node super suppressor'                            => array($keyword,                      ContributionActionInterface::CREATE, array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Add) : Content contributor'                              => array($keyword,                      ContributionActionInterface::CREATE, array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     null, VoterInterface::ACCESS_DENIED),
            'Bad role (Add) : Content super editor'                             => array($keyword,                      ContributionActionInterface::CREATE, array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Add) : Content super suppressor'                         => array($keyword,                      ContributionActionInterface::CREATE, array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), null, VoterInterface::ACCESS_DENIED),
            'Bad role (Add) : Site Admin'                                       => array($keyword,                      ContributionActionInterface::CREATE, array(ContributionRoleInterface::SITE_ADMIN),              null, VoterInterface::ACCESS_DENIED),
            'Bad role (Add) : Trash Restorer'                                   => array($keyword,                      ContributionActionInterface::CREATE, array(ContributionRoleInterface::TRASH_RESTORER),          null, VoterInterface::ACCESS_DENIED),
            'Bad role (Add) : Trash Suppressor'                                 => array($keyword,                      ContributionActionInterface::CREATE, array(ContributionRoleInterface::TRASH_SUPRESSOR),         null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : None'                                            => array($keyword,                      ContributionActionInterface::EDIT,   array(),                                                   null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Node contributor'                                => array($keyword,                      ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_CONTRIBUTOR),        null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Node super editor'                               => array($keyword,                      ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_SUPER_EDITOR),       null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Node super suprpessor'                           => array($keyword,                      ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Content contributor'                             => array($keyword,                      ContributionActionInterface::EDIT,   array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Content super editor'                            => array($keyword,                      ContributionActionInterface::EDIT,   array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Content super suppressor'                        => array($keyword,                      ContributionActionInterface::EDIT,   array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Site Admin'                                      => array($keyword,                      ContributionActionInterface::EDIT,   array(ContributionRoleInterface::SITE_ADMIN),              null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Trash Restorer'                                  => array($keyword,                      ContributionActionInterface::EDIT,   array(ContributionRoleInterface::TRASH_RESTORER),          null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Trash Suppressor'                                => array($keyword,                      ContributionActionInterface::EDIT,   array(ContributionRoleInterface::TRASH_SUPRESSOR),         null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : None'                                          => array($keyword,                      ContributionActionInterface::DELETE, array(),                                                   null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Node contributor'                              => array($keyword,                      ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_CONTRIBUTOR),        null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Node super editor'                             => array($keyword,                      ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_SUPER_EDITOR),       null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Node super suppressor'                         => array($keyword,                      ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Content contributor'                           => array($keyword,                      ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Content super editor'                          => array($keyword,                      ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Content super suppressor'                      => array($keyword,                      ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Site Admin'                                    => array($keyword,                      ContributionActionInterface::DELETE, array(ContributionRoleInterface::SITE_ADMIN),              null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Trash Restorer'                                => array($keyword,                      ContributionActionInterface::DELETE, array(ContributionRoleInterface::TRASH_RESTORER),          null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Trash Suppressor'                              => array($keyword,                      ContributionActionInterface::DELETE, array(ContributionRoleInterface::TRASH_SUPRESSOR),         null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) keyword entity type: Node contributor'           => array(KeywordInterface::ENTITY_TYPE, ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_CONTRIBUTOR),        null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) keyword entity type: Node super editor'          => array(KeywordInterface::ENTITY_TYPE, ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_SUPER_EDITOR),       null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) keyword entity type: Node super suppressor'      => array(KeywordInterface::ENTITY_TYPE, ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) keyword entity type: Content contributor'        => array(KeywordInterface::ENTITY_TYPE, ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) keyword entity type: Content super editor'       => array(KeywordInterface::ENTITY_TYPE, ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) keyword entity type: Content super suppressor'   => array(KeywordInterface::ENTITY_TYPE, ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) keyword entity type: Site Admin'                 => array(KeywordInterface::ENTITY_TYPE, ContributionActionInterface::DELETE, array(ContributionRoleInterface::SITE_ADMIN),              null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) keyword entity type: Trash Restorer'             => array(KeywordInterface::ENTITY_TYPE, ContributionActionInterface::DELETE, array(ContributionRoleInterface::TRASH_RESTORER),          null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) keyword entity type: Trash Suppressor'           => array(KeywordInterface::ENTITY_TYPE, ContributionActionInterface::DELETE, array(ContributionRoleInterface::TRASH_SUPRESSOR),         null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) keyword entity type: Node contributor'             => array(KeywordInterface::ENTITY_TYPE, ContributionActionInterface::EDIT, array(ContributionRoleInterface::NODE_CONTRIBUTOR),        null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) keyword entity type: Node super editor'            => array(KeywordInterface::ENTITY_TYPE, ContributionActionInterface::EDIT, array(ContributionRoleInterface::NODE_SUPER_EDITOR),       null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) keyword entity type: Node super suppressor'        => array(KeywordInterface::ENTITY_TYPE, ContributionActionInterface::EDIT, array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) keyword entity type: Content contributor'          => array(KeywordInterface::ENTITY_TYPE, ContributionActionInterface::EDIT, array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) keyword entity type: Content super editor'         => array(KeywordInterface::ENTITY_TYPE, ContributionActionInterface::EDIT, array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) keyword entity type: Content super suppressor'     => array(KeywordInterface::ENTITY_TYPE, ContributionActionInterface::EDIT, array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) keyword entity type: Site Admin'                   => array(KeywordInterface::ENTITY_TYPE, ContributionActionInterface::EDIT, array(ContributionRoleInterface::SITE_ADMIN),              null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) keyword entity type: Trash Restorer'               => array(KeywordInterface::ENTITY_TYPE, ContributionActionInterface::EDIT, array(ContributionRoleInterface::TRASH_RESTORER),          null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) keyword entity type: Trash Suppressor'             => array(KeywordInterface::ENTITY_TYPE, ContributionActionInterface::EDIT, array(ContributionRoleInterface::TRASH_SUPRESSOR),         null, VoterInterface::ACCESS_DENIED),            'Bad role (Delete) keyword entity type: Node contributor'        => array(KeywordInterface::ENTITY_TYPE, ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_CONTRIBUTOR),        null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) api client entity type: Node super editor'       => array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_SUPER_EDITOR),       null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) api client entity type: Node super suppressor'   => array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) api client entity type: Content contributor'     => array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) api client entity type: Content super editor'    => array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) api client entity type: Content super suppressor'=> array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) api client entity type: Site Admin'              => array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::DELETE, array(ContributionRoleInterface::SITE_ADMIN),              null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) api client entity type: Trash Restorer'          => array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::DELETE, array(ContributionRoleInterface::TRASH_RESTORER),          null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) api client entity type: Trash Suppressor'        => array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::DELETE, array(ContributionRoleInterface::TRASH_SUPRESSOR),         null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) api client entity type: Node contributor'          => array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::EDIT, array(ContributionRoleInterface::NODE_CONTRIBUTOR),        null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) api client entity type: Node super editor'         => array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::EDIT, array(ContributionRoleInterface::NODE_SUPER_EDITOR),       null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) api client entity type: Node super suppressor'     => array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::EDIT, array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) api client entity type: Content contributor'       => array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::EDIT, array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) api client entity type: Content super editor'      => array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::EDIT, array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) api client entity type: Content super suppressor'  => array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::EDIT, array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) api client entity type: Site Admin'                => array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::EDIT, array(ContributionRoleInterface::SITE_ADMIN),              null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) api client entity type: Trash Restorer'            => array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::EDIT, array(ContributionRoleInterface::TRASH_RESTORER),          null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) api client entity type: Trash Suppressor'          => array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::EDIT, array(ContributionRoleInterface::TRASH_SUPRESSOR),         null, VoterInterface::ACCESS_DENIED),
        );
    }

    /**
     * @return array
     */
    protected function getOkVotes()
    {
        $keyword = $this->createPhakeKeyword();
        $client = $this->createPhakeApiClient();

        return array(
            'Ok : Read keyword'             => array($keyword,                        ContributionActionInterface::READ,   array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Add keyword'              => array($keyword,                        ContributionActionInterface::CREATE, array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Edit keyword'             => array($keyword,                        ContributionActionInterface::EDIT,   array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Delete keyword'           => array($keyword,                        ContributionActionInterface::DELETE, array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Read api client'          => array($client,                         ContributionActionInterface::READ,   array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Add api client'           => array($client,                         ContributionActionInterface::CREATE, array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Edit api client'          => array($client,                         ContributionActionInterface::EDIT,   array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Delete api client'        => array($client,                         ContributionActionInterface::DELETE, array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Read entity keyword'      => array(KeywordInterface::ENTITY_TYPE,  ContributionActionInterface::READ,   array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Add entity keyword'       => array(KeywordInterface::ENTITY_TYPE,  ContributionActionInterface::CREATE, array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Edit entity keyword'      => array(KeywordInterface::ENTITY_TYPE,  ContributionActionInterface::EDIT,   array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Delete entity keyword'    => array(KeywordInterface::ENTITY_TYPE,  ContributionActionInterface::DELETE, array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Read api entity client'   => array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::READ,   array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Add api entity client'    => array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::CREATE, array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Edit api entity client'   => array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Delete api entity client' => array(ApiClientInterface::ENTITY_TYPE, ContributionActionInterface::DELETE, array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
        );
    }
}
