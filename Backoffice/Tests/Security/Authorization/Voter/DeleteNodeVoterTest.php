<?php

namespace OpenOrchestra\Backoffice\Tests\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\Backoffice\Security\Authorization\Voter\DeleteNodeVoter;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Phake;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Test DeleteNodeVoterTest
 */
class DeleteNodeVoterTest extends AbstractBaseTestCase
{
    /**
     * @var DeleteNodeVoter
     */
    protected $voter;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->voter = new DeleteNodeVoter();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Security\Core\Authorization\Voter\VoterInterface', $this->voter);
    }

    /**
     * @param bool   $supports
     * @param string $role
     *
     * @dataProvider provideSupportsAndRole
     */
    public function testSupportsAttributes($supports, $role)
    {
        $this->assertSame($supports, $this->voter->supportsAttribute($role));
    }

    /**
     * @return array
     */
    public function provideSupportsAndRole()
    {
        return array(
            array(true, TreeNodesPanelStrategy::ROLE_ACCESS_DELETE_NODE),
            array(false, TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE),
            array(false, TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE),
            array(false, TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE),
            array(false, TreeNodesPanelStrategy::ROLE_ACCESS_MOVE_TREE),
            array(false, 'foo'),
        );
    }

    /**
     * @param bool   $supports
     * @param string $class
     *
     * @dataProvider provideSupportsAndClass
     */
    public function testSupportsClass($supports, $class)
    {
        $object = Phake::mock($class);

        $this->assertSame($supports, $this->voter->supportsClass($object));
    }

    /**
     * @return array
     */
    public function provideSupportsAndClass()
    {
        return array(
            array(true, 'OpenOrchestra\ModelInterface\Model\NodeInterface'),
            array(false, 'OpenOrchestra\ModelInterface\Model\EmbedStatusInterface'),
            array(false, 'OpenOrchestra\ModelInterface\Model\StatusableInterface'),
            array(false, 'OpenOrchestra\ModelInterface\Model\RoleInterface'),
            array(false, 'OpenOrchestra\ModelInterface\Model\ContentInterface'),
            array(false, 'stdClass'),
        );
    }

    /**
     * @param int    $response
     * @param string $nodeId
     *
     * @dataProvider provideResponseAndUsageFound
     */
    public function testVote($response, $nodeId)
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getNodeId()->thenReturn($nodeId);
        $roles = array(TreeNodesPanelStrategy::ROLE_ACCESS_DELETE_NODE);
        $token = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');

        $this->assertSame($response, $this->voter->vote($token, $node, $roles));
    }

    /**
     * @return array
     */
    public function provideResponseAndUsageFound()
    {
        return array(
            array(VoterInterface::ACCESS_GRANTED, 'fakeNodeId'),
            array(VoterInterface::ACCESS_DENIED, NodeInterface::ROOT_NODE_ID),
        );
    }
}
