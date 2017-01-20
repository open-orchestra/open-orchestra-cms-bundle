<?php

namespace OpenOrchestra\Workflow\Tests\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use OpenOrchestra\Workflow\Security\Authorization\Voter\NodeWorkflowVoter;
use Phake;
use OpenOrchestra\Backoffice\Tests\Security\Authorization\Voter\AbstractVoterTest;

/**
 * Class NodeWorkflowVoterTest
 */
class NodeWorkflowVoterTest extends AbstractVoterTest
{
    protected $workflowRepository;
    protected $profile;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();

        $this->profile = Phake::mock('OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface');
        $profileCollection = Phake::mock('OpenOrchestra\ModelInterface\Model\WorkflowProfileCollectionInterface');
        Phake::when($profileCollection)->getProfiles()->thenReturn(array($this->profile));
        Phake::when($this->group)->getWorkflowProfileCollection(Phake::anyParameters())->thenReturn($profileCollection);

        $this->workflowRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\WorkflowProfileRepositoryInterface');

        $this->voter = new NodeWorkflowVoter($this->accessDecisionManager, $this->perimeterManager, $this->workflowRepository);
    }

    /**
     * @param mixed   $object       the object to secure
     * @param string  $attribute    the action to test
     * @param array   $roles        the user's roles
     * @param boolean $inPerimeter  if the object is in the user perimeter
     * @param int     $expectedVote the expected result of the vote (GRANTED, ABSTAIN or DENIED)
     * @param boolean $inProfile    if the transition is in a user's profile
     *
     * @dataProvider provideDataToVote
     */
    public function testVote($object, $attribute, array $roles, $inPerimeter, $expectedVote, $inProfile = true)
    {
        Phake::when($this->profile)->hasTransition(Phake::anyParameters())->thenReturn($inProfile);
        Phake::when($this->workflowRepository)->hasTransition(Phake::anyParameters())->thenReturn($inProfile);

        parent::testVote($object, $attribute, $roles, $inPerimeter, $expectedVote);
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
            'Bad subject : Content'          => array($content    , $status, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Trash Item'       => array($trashItem  , $status, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Site'             => array($site       , $status, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Redirection'      => array($redirection, $status, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Log'              => array($log        , $status, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : User'             => array($user       , $status, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Group'            => array($group      , $status, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Keyword'          => array($keyword    , $status, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Api client'       => array($client     , $status, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Content type'     => array($contentType, $status, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Workflow profile' => array($profile    , $status, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Status'           => array($status     , $status, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
        );
    }

    /**
     * @return array
     */
    protected function getNotSupportedAttributes()
    {
        $node = $this->createPhakeNode();

        return array(
            'Bad attribute : Read'          => array($node, ContributionActionInterface::READ         , array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad attribute : Edit'          => array($node, ContributionActionInterface::EDIT         , array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad attribute : Create'        => array($node, ContributionActionInterface::CREATE       , array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad attribute : Delete'        => array($node, ContributionActionInterface::DELETE       , array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad attribute : Trash Purge'   => array($node, ContributionActionInterface::TRASH_PURGE  , array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad attribute : Trash Restore' => array($node, ContributionActionInterface::TRASH_RESTORE, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
        );
    }

    /**
     * @return array
     */
    protected function getNotInPerimeter()
    {
        $node = $this->createPhakeNode();
        $status = $this->createPhakeStatus();

        return array(
            'Not in perimeter : Contributor' => array($node, $status, array(ContributionRoleInterface::NODE_CONTRIBUTOR), false, VoterInterface::ACCESS_DENIED, false),
        );
    }

    /**
     * @return array
     */
    protected function getBadRoles()
    {
        return array();
    }

    /**
     * @return array
     */
    protected function getOkVotes()
    {
        $status = $this->createPhakeStatus();
        $node = $this->createPhakeNode();
        Phake::when($node)->getStatus()->thenReturn($status);

        return array(
            'Ok : Contributor' => array($node, $status, array(ContributionRoleInterface::NODE_CONTRIBUTOR), true , VoterInterface::ACCESS_GRANTED, true),
            'Ok : Developper'  => array($node, $status, array(ContributionRoleInterface::DEVELOPER)       , false, VoterInterface::ACCESS_GRANTED, true),
        );
    }
}
