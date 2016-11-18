<?php

namespace OpenOrchestra\Backoffice\Tests\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Tests\Manager\AbstractVoterTest;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use OpenOrchestra\Backoffice\Security\Authorization\Voter\ContentVoter;

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

        $this->voter = new ContentVoter();
    }

    /**
     * @return array
     */
    protected function getNotSupportedSubjects()
    {
        $node = $this->createPhakeNode();
        $trashItem = $this->createPhakeTrashItem();

        return array(
            'Bad subject : Node'       => array($node,      ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Trash Item' => array($trashItem, ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
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
            'Not in perimeter : Edit self'    => array($contentSelf, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Delete self'  => array($contentSelf, ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Edit other'   => array($content,     ContributionActionInterface::EDIT,   array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),    false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Delete other' => array($content,     ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), false, VoterInterface::ACCESS_DENIED),
        );
    }

    /**
     * @return array
     */
    protected function getBadRoles()
    {
        $content = $this->createPhakeContent();

        return array(
            'Bad role (Edit) : None'                   => array($content, ContributionActionInterface::EDIT,   array(),                                                true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Node contributor'       => array($content, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_CONTRIBUTOR),     true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Node super editor'      => array($content, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_SUPER_EDITOR),    true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Node super supressor'   => array($content, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR), true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Site Admin'             => array($content, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::SITE_ADMIN),           true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Trash Restorer'         => array($content, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::TRASH_RESTORER),       true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Trash Supressor'        => array($content, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::TRASH_SUPRESSOR),      true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : None'                 => array($content, ContributionActionInterface::DELETE, array(),                                                true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Node contributor'     => array($content, ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_CONTRIBUTOR),     true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Node super editor'    => array($content, ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_SUPER_EDITOR),    true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Node super supressor' => array($content, ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR), true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Site Admin'           => array($content, ContributionActionInterface::DELETE, array(ContributionRoleInterface::SITE_ADMIN),           true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Trash Restorer'       => array($content, ContributionActionInterface::DELETE, array(ContributionRoleInterface::TRASH_RESTORER),       true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : Trash Supressor'      => array($content, ContributionActionInterface::DELETE, array(ContributionRoleInterface::TRASH_SUPRESSOR),      true, VoterInterface::ACCESS_DENIED),
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
            'Ok : Read'         => array($content,     ContributionActionInterface::READ,   array(),                                                   true, VoterInterface::ACCESS_GRANTED),
            'Ok : Add self'     => array($contentSelf, ContributionActionInterface::ADD,    array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     true, VoterInterface::ACCESS_GRANTED),
            'Ok : Edit self'    => array($contentSelf, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     true, VoterInterface::ACCESS_GRANTED),
            'Ok : Delete self'  => array($contentSelf, ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     true, VoterInterface::ACCESS_GRANTED),
            'Ok : Edit other'   => array($content,     ContributionActionInterface::EDIT,   array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),    true, VoterInterface::ACCESS_GRANTED),
            'Ok : Delete other' => array($content,     ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), true, VoterInterface::ACCESS_GRANTED),
        );
    }
}
