<?php

namespace OpenOrchestra\Backoffice\Tests\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use OpenOrchestra\Backoffice\Security\Authorization\Voter\PlatformAdministrationVoter;

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

        $this->voter = new PlatformAdministrationVoter();
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
            'Bad subject : Node'             => array($node,        ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Content'          => array($content,     ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Trash Item'       => array($trashItem,   ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Site'             => array($site,        ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Redirection'      => array($redirection, ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Log'              => array($log,         ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : User'             => array($user,        ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Group'            => array($group,       ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Content type'     => array($contentType, ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Workflow profile' => array($profile,     ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Status'           => array($status,      ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), null, VoterInterface::ACCESS_ABSTAIN),
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
            'Bad role (Add) : None'                       => array($keyword, ContributionActionInterface::CREATE, array(),                                                   null, VoterInterface::ACCESS_DENIED),
            'Bad role (Add) : Node contributor'           => array($keyword, ContributionActionInterface::CREATE, array(ContributionRoleInterface::NODE_CONTRIBUTOR),        null, VoterInterface::ACCESS_DENIED),
            'Bad role (Add) : Node super editor'          => array($keyword, ContributionActionInterface::CREATE, array(ContributionRoleInterface::NODE_SUPER_EDITOR),       null, VoterInterface::ACCESS_DENIED),
            'Bad role (Add) : Node super supressor'       => array($keyword, ContributionActionInterface::CREATE, array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Add) : Content contributor'        => array($keyword, ContributionActionInterface::CREATE, array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     null, VoterInterface::ACCESS_DENIED),
            'Bad role (Add) : Content super editor'       => array($keyword, ContributionActionInterface::CREATE, array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Add) : Content super supressor'    => array($keyword, ContributionActionInterface::CREATE, array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), null, VoterInterface::ACCESS_DENIED),
            'Bad role (Add) : Site Admin'                 => array($keyword, ContributionActionInterface::CREATE, array(ContributionRoleInterface::SITE_ADMIN),              null, VoterInterface::ACCESS_DENIED),
            'Bad role (Add) : Trash Restorer'             => array($keyword, ContributionActionInterface::CREATE, array(ContributionRoleInterface::TRASH_RESTORER),          null, VoterInterface::ACCESS_DENIED),
            'Bad role (Add) : Trash Supressor'            => array($keyword, ContributionActionInterface::CREATE, array(ContributionRoleInterface::TRASH_SUPRESSOR),         null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : None'                      => array($keyword, ContributionActionInterface::EDIT,   array(),                                                   null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Node contributor'          => array($keyword, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_CONTRIBUTOR),        null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Node super editor'         => array($keyword, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_SUPER_EDITOR),       null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Node super supressor'      => array($keyword, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Content contributor'       => array($keyword, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Content super editor'      => array($keyword, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Content super supressor'   => array($keyword, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Site Admin'                => array($keyword, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::SITE_ADMIN),              null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Trash Restorer'            => array($keyword, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::TRASH_RESTORER),          null, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Trash Supressor'           => array($keyword, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::TRASH_SUPRESSOR),         null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : None'                    => array($keyword, ContributionActionInterface::DELETE, array(),                                                   null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Node contributor'        => array($keyword, ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_CONTRIBUTOR),        null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Node super editor'       => array($keyword, ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_SUPER_EDITOR),       null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Node super supressor'    => array($keyword, ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Content contributor'     => array($keyword, ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Content super editor'    => array($keyword, ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),    null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Content super supressor' => array($keyword, ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Site Admin'              => array($keyword, ContributionActionInterface::DELETE, array(ContributionRoleInterface::SITE_ADMIN),              null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Trash Restorer'          => array($keyword, ContributionActionInterface::DELETE, array(ContributionRoleInterface::TRASH_RESTORER),          null, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Trash Supressor'         => array($keyword, ContributionActionInterface::DELETE, array(ContributionRoleInterface::TRASH_SUPRESSOR),         null, VoterInterface::ACCESS_DENIED),
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
            'Ok : Read keyword'      => array($keyword, ContributionActionInterface::READ,   array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Add keyword'       => array($keyword, ContributionActionInterface::CREATE, array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Edit keyword'      => array($keyword, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Delete keyword'    => array($keyword, ContributionActionInterface::DELETE, array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Read api client'   => array($client,  ContributionActionInterface::READ,   array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Add api client'    => array($client,  ContributionActionInterface::CREATE, array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Edit api client'   => array($client,  ContributionActionInterface::EDIT,   array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
            'Ok : Delete api client' => array($client,  ContributionActionInterface::DELETE, array(ContributionRoleInterface::PLATFORM_ADMIN), null, VoterInterface::ACCESS_GRANTED),
        );
    }
}
