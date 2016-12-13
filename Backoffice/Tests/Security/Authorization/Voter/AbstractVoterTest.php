<?php

namespace OpenOrchestra\Backoffice\Tests\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class AbstractVoterTest
 */
abstract class AbstractVoterTest extends AbstractBaseTestCase
{
    protected $voter;

    protected $perimeterManager;
    protected $group;
    protected $token;
    protected $user;
    protected $accessDecisionManager;
    protected $perimeter;
    protected $username = 'User Name';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->perimeter = Phake::mock('OpenOrchestra\Backoffice\Model\PerimeterInterface');
        Phake::when($this->perimeter)->getItems(Phake::anyParameters())->thenReturn(array());

        $this->perimeterManager = Phake::mock('OpenOrchestra\Backoffice\Perimeter\PerimeterManager');
        Phake::when($this->perimeterManager)->createPerimeter(Phake::anyParameters())->thenReturn(Phake::mock('OpenOrchestra\Backoffice\Model\PerimeterInterface'));

        $this->group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($this->group)->getPerimeter(Phake::anyParameters())->thenReturn($this->perimeter);

        $this->user = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($this->user)->getGroups()->thenReturn(array($this->group));
        Phake::when($this->user)->getUsername()->thenReturn($this->username);

        $this->token = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        Phake::when($this->token)->getUser()->thenReturn($this->user);

        $this->accessDecisionManager = Phake::mock('Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface');
    }

    /**
     * @param mixed  $object       the object to secure
     * @param string $attribute    the action to test
     * @param array  $roles        the user's roles
     * @param bool   $inPerimeter  if the object is in the user perimeter
     * @param int    $expectedVote the expected result of the vote (GRANTED, ABSTAIN or DENIED)
     *
     * @dataProvider provideDataToVote
     */
    public function testVote($object, $attribute, array $roles, $inPerimeter, $expectedVote)
    {
        if (in_array(ContributionRoleInterface::PLATFORM_ADMIN, $roles)) {
            Phake::when($this->accessDecisionManager)->decide(Phake::anyParameters())->thenReturn(true);
        }
        Phake::when($this->token)->getRoles()->thenReturn($roles);

        Phake::when($this->perimeterManager)->isInPerimeter(Phake::anyParameters())->thenReturn($inPerimeter);

        $vote = $this->voter->vote($this->token, $object, array($attribute));

        $this->assertEquals($expectedVote, $vote);
    }

    /**
     * Provide data for vote test
     * It merges several subsets of data for better maintain.
     * The subset are given by abstract methods
     *
     * @return array
     */
    public function provideDataToVote()
    {
        return array_merge(
            $this->getNotSupportedSubjects(),
            $this->getNotSupportedAttributes(),
            $this->getNotInPerimeter(),
            $this->getBadRoles(),
            $this->getOkVotes()
        );
    }

    /**
     * Data set of not supported objects leading to a ABSTAIN access
     *
     * @return array
     */
    abstract protected function getNotSupportedSubjects();

    /**
     * Data set of not supported actions leading to a ABSTAIN access
     *
     * @return array
     */
    abstract protected function getNotSupportedAttributes();

    /**
     * Data set of items not in users perimeter leading to a DENIED access
     *
     * @return array
     */
    abstract protected function getNotInPerimeter();

        /**
     * Data set of bad roles leading to a DENIED access
     *
     * @return array
     */
    abstract protected function getBadRoles();

    /**
     * Data set leading to a GRANTED access
     *
     * @return array
     */
    abstract protected function getOkVotes();

    /**
     * Create a Phake node
     *
     * @param bool $owner
     *
     * @return Phake_IMock
     */
    protected function createPhakeNode($owner = false)
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

        if ($owner) {
            Phake::when($node)->getCreatedBy()->thenReturn($this->username);
        }

        return $node;
    }

    /**
     * Create a Phake node
     *
     * @param bool $owner
     *
     * @return Phake_IMock
     */
    protected function createPhakeNodeNotHydrated($owner = false)
    {
        $node = array(
            'nodeId' => 'fakeNodeId',
            'path' => 'fakePath'
        );

        if ($owner) {
            $node['createdBy'] = $this->username;
        }

        return $node;
    }

    /**
     * Create a Phake content
     *
     * @param bool $owner
     *
     * @return Phake_IMock
     */
    protected function createPhakeContent($owner = false)
    {
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');

        if ($owner) {
            Phake::when($content)->getCreatedBy()->thenReturn($this->username);
        }

        return $content;
    }

    /**
     * Create a Phake trash item
     *
     * @return Phake_IMock
     */
    protected function createPhakeTrashItem()
    {
        return Phake::mock('OpenOrchestra\ModelInterface\Model\TrashItemInterface');
    }

    /**
     * Create a Phake site
     *
     * @return Phake_IMock
     */
    protected function createPhakeSite()
    {
        return Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
    }

    /**
     * Create a Phake redirection
     *
     * @return Phake_IMock
     */
    protected function createPhakeRedirection()
    {
        return Phake::mock('OpenOrchestra\ModelInterface\Model\RedirectionInterface');
    }

    /**
     * Create a Phake log
     *
     * @return Phake_IMock
     */
    protected function createPhakeLog()
    {
        return Phake::mock('OpenOrchestra\LogBundle\Model\LogInterface');
    }

    /**
     * Create a Phake user
     *
     * @return Phake_IMock
     */
    protected function createPhakeUser()
    {
        $group = $this->createPhakeGroup();

        $user = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($user)->getGroups()->thenReturn(array($group));

        return $user;
    }

    /**
     * Create a Phake group
     *
     * @return Phake_IMock
     */
    protected function createPhakeGroup()
    {
        $site = $this->createPhakeSite();

        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($group)->getSite()->thenReturn($site);

        return $group;
    }

    /**
     * Create a Phake keyword
     *
     * @return Phake_IMock
     */
    protected function createPhakeKeyword()
    {
        return Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
    }

    /**
     * Create a Phake api client
     *
     * @return Phake_IMock
     */
    protected function createPhakeApiClient()
    {
        return Phake::mock('OpenOrchestra\BaseApi\Model\ApiClientInterface');
    }

    /**
     * Create a Phake content type
     *
     * @return Phake_IMock
     */
    protected function createPhakeContentType()
    {
        return Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
    }

    /**
     * Create a Phake workflow profile
     *
     * @return Phake_IMock
     */
    protected function createPhakeWorkflowProfile()
    {
        return Phake::mock('OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface');
    }

    /**
     * Create a Phake status
     *
     * @return Phake_IMock
     */
    protected function createPhakeStatus()
    {
        return Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
    }

    /**
     * Create a Phake transition
     *
     * @return Phake_IMock
     */
    protected function createPhakeTransition()
    {
        return Phake::mock('OpenOrchestra\ModelInterface\Model\WorkflowTransitionInterface');
    }
}
