<?php

namespace OpenOrchestra\Backoffice\Tests\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\Authorization\Voter\NodeVoter;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class NodeVoterTest
 */
class NodeVoterTest extends AbstractVoterTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();

        $this->voter = new NodeVoter($this->perimeterManager);
    }

    /**
     * @return array
     */
    protected function getNotSupportedSubjects()
    {
        $content = $this->createPhakeContent();
        $trashItem = $this->createPhakeTrashItem();
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
            'Bad subject : Content'          => array($content,     ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Trash Item'       => array($trashItem,   ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Site'             => array($site,        ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Redirection'      => array($redirection, ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Log'              => array($log,         ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : User'             => array($user,        ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Group'            => array($group,       ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Keyword'          => array($keyword,     ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Api client'       => array($client,      ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Content type'     => array($contentType, ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Workflow profile' => array($profile,     ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Status'           => array($status,      ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
        );
    }

    /**
     * @return array
     */
    protected function getNotSupportedAttributes()
    {
        $node = $this->createPhakeNode();

        return array(
            'Bad action : Trash Purge'   => array($node, ContributionActionInterface::TRASH_PURGE,   array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad action : Trash Restore' => array($node, ContributionActionInterface::TRASH_RESTORE, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
        );
    }

    /**
     * @return array
     */
    protected function getNotInPerimeter()
    {
        $node = $this->createPhakeNode();
        $nodeSelf = $this->createPhakeNode(true);

        return array(
            'Not in perimeter : Read self'    => array($nodeSelf, ContributionActionInterface::READ,   array(ContributionRoleInterface::NODE_CONTRIBUTOR),     false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Edit self'    => array($nodeSelf, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_CONTRIBUTOR),     false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Delete self'  => array($nodeSelf, ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_CONTRIBUTOR),     false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Edit other'   => array($node,     ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_SUPER_EDITOR),    false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Delete other' => array($node,     ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR), false, VoterInterface::ACCESS_DENIED),
        );
    }

    /**
     * @return array
     */
    protected function getBadRoles()
    {
        $node = $this->createPhakeNode();

        return array(
            'Bad role (Edit) : None'                      => array($node, ContributionActionInterface::EDIT,   array(),                                                   true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Content contributor'       => array($node, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Content super editor'      => array($node, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),    true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Content super supressor'   => array($node, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Site Admin'                => array($node, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::SITE_ADMIN),              true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Trash Restorer'            => array($node, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::TRASH_RESTORER),          true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Trash Supressor'           => array($node, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::TRASH_SUPRESSOR),         true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : None'                    => array($node, ContributionActionInterface::DELETE, array(),                                                   true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Content contributor'     => array($node, ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Content super editor'    => array($node, ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),    true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Content super supressor' => array($node, ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Site Admin'              => array($node, ContributionActionInterface::DELETE, array(ContributionRoleInterface::SITE_ADMIN),              true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Trash Restorer'          => array($node, ContributionActionInterface::DELETE, array(ContributionRoleInterface::TRASH_RESTORER),          true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Trash Supressor'         => array($node, ContributionActionInterface::DELETE, array(ContributionRoleInterface::TRASH_SUPRESSOR),         true, VoterInterface::ACCESS_DENIED),
        );
    }

    /**
     * @return array
     */
    protected function getOkVotes()
    {
        $node = $this->createPhakeNode();
        $nodeSelf = $this->createPhakeNode(true);

        return array(
            'Ok : Read'         => array($node,     ContributionActionInterface::READ,   array(ContributionRoleInterface::NODE_CONTRIBUTOR),     true, VoterInterface::ACCESS_GRANTED),
            'Ok : Add self'     => array($nodeSelf, ContributionActionInterface::CREATE, array(ContributionRoleInterface::NODE_CONTRIBUTOR),     true, VoterInterface::ACCESS_GRANTED),
            'Ok : Edit self'    => array($nodeSelf, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_CONTRIBUTOR),     true, VoterInterface::ACCESS_GRANTED),
            'Ok : Delete self'  => array($nodeSelf, ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_CONTRIBUTOR),     true, VoterInterface::ACCESS_GRANTED),
            'Ok : Edit other'   => array($node,     ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_SUPER_EDITOR),    true, VoterInterface::ACCESS_GRANTED),
            'Ok : Delete other' => array($node,     ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR), true, VoterInterface::ACCESS_GRANTED),
        );
    }
}
