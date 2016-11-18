<?php

namespace OpenOrchestra\Backoffice\Tests\Manager;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class AbstractVoterTest
 */
abstract class AbstractVoterTest extends AbstractBaseTestCase
{
    protected $voter;

    protected $token;
    protected $user;
    protected $perimeter;
    protected $username = 'User Name';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->perimeter = Phake::mock('OpenOrchestra\Backoffice\Model\PerimeterInterface');

        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($group)->getPerimeter(Phake::anyParameters())->thenReturn($this->perimeter);

        $this->user = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($this->user)->getGroups()->thenReturn(array($group));
        Phake::when($this->user)->getUsername()->thenReturn($this->username);

        $this->token = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        Phake::when($this->token)->getUser()->thenReturn($this->user);
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
        foreach ($roles as $role) {
            Phake::when($this->user)->hasRole($role)->thenReturn(true);
        }

        Phake::when($this->perimeter)->contains(Phake::anyParameters())->thenReturn($inPerimeter);

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
     * @param bool $owner
     *
     * @return Phake_IMock
     */
    protected function createPhakeTrashItem()
    {
        return Phake::mock('OpenOrchestra\ModelInterface\Model\TrashItemInterface');
    }
}