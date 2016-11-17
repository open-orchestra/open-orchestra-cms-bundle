<?php

namespace OpenOrchestra\Backoffice\Tests\Security\Authorization\Voter;

use Phake;
use OpenOrchestra\Backoffice\Security\Authorization\Voter\NodeVoter;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Tests\Manager\AbstractVoterTest;
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

        $this->voter = new NodeVoter();
    }

    /**
     * @return array
     */
    protected function getNotSupportedSubjects()
    {
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        $trashItem = Phake::mock('OpenOrchestra\ModelInterface\Model\TrashItemInterface');

        return array(
            'Bad subject : Content'    => array($content,   ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Trash Item' => array($trashItem, ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
        );
    }

    /**
     * @return array
     */
    protected function getNotSupportedAttributes()
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

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
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

        $nodeSelf = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($nodeSelf)->getCreatedBy()->thenReturn($this->username);

        return array(
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
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

        return array(
            'Bad role (Edit) : None'                      => array($node, ContributionActionInterface::EDIT,   array(),                                                   true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Content contributor'       => array($node, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),     true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Content super editor'      => array($node, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),    true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Content super supressor'   => array($node, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR), true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Site Admin'                => array($node, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::SITE_ADMIN),              true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Trash Restorer'            => array($node, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::TRASH_RESTORER),          true, VoterInterface::ACCESS_DENIED),
            'Bad role (Edit) : Trash Supressor'           => array($node, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::TRASH_SUPRESSOR),         true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : None'                    => array($node, ContributionActionInterface::DELETE, array(),                                                   true, VoterInterface::ACCESS_DENIED),
            'Bad role (Delete) : None'                    => array($node, ContributionActionInterface::DELETE, array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),    true, VoterInterface::ACCESS_DENIED),
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
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

        $nodeSelf = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($nodeSelf)->getCreatedBy()->thenReturn($this->username);

        return array(
            'Ok : Read'         => array($node,     ContributionActionInterface::READ,   array(),                                                false, VoterInterface::ACCESS_GRANTED),
            'Ok : Add self'     => array($nodeSelf, ContributionActionInterface::ADD,    array(ContributionRoleInterface::NODE_CONTRIBUTOR),     true,  VoterInterface::ACCESS_GRANTED),
            'Ok : Edit self'    => array($nodeSelf, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_CONTRIBUTOR),     true,  VoterInterface::ACCESS_GRANTED),
            'Ok : Delete self'  => array($nodeSelf, ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_CONTRIBUTOR),     true,  VoterInterface::ACCESS_GRANTED),
            'Ok : Edit other'   => array($node,     ContributionActionInterface::EDIT,   array(ContributionRoleInterface::NODE_SUPER_EDITOR),    true,  VoterInterface::ACCESS_GRANTED),
            'Ok : Delete other' => array($node,     ContributionActionInterface::DELETE, array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR), true,  VoterInterface::ACCESS_GRANTED),
        );
    }
}
