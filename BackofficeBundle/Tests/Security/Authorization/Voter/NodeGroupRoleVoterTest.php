<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\BackofficeBundle\Security\Authorization\Voter\NodeGroupRoleVoter;
use Phake;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Test NodeGroupRoleVoterTest
 */
class NodeGroupRoleVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeGroupRoleVoter
     */
    protected $voter;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->voter = new NodeGroupRoleVoter();
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
     * @param string $class
     *
     * @dataProvider provideClassName
     */
    public function testSupportsClass($supports, $class)
    {
        $this->assertSame($supports, $this->voter->supportsClass($class));
    }

    /**
     * @return array
     */
    public function provideClassName()
    {
        return array(
            array(false, 'StdClass'),
            array(false, 'class'),
            array(false, 'string'),
            array(false, 'Symfony\Component\Security\Core\Authorization\Voter\VoterInterface'),
            array(false, 'OpenOrchestra\BackofficeBundle\Model\GroupInterface'),
            array(false, 'OpenOrchestra\ModelInterface\Model\NodeInterface'),
            array(true, 'OpenOrchestra\ModelBundle\Document\Node'),
            array(false, 'OpenOrchestra\ModelInterface\Model\ReadNodeInterface'),
        );
    }

    /**
     * @param string $attribute
     * @param bool   $supports
     *
     * @dataProvider provideAttributeAndSupport
     */
    public function testSupportsAttribute($attribute, $supports)
    {
        $this->assertSame($supports, $this->voter->supportsAttribute($attribute));
    }

    /**
     * @return array
     */
    public function provideAttributeAndSupport()
    {
        return array(
            array('ROLE_ACCESS_GENERAL_NODE', true),
            array('ROLE_ACCESS_REDIRECTION', false),
            array('ROLE_ACCESS_TREE_NODE', true),
            array('ROLE_ACCESS_CREATE_NODE', true),
            array('ROLE_ACCESS_UPDATE_NODE', true),
            array('ROLE_ACCESS_DELETE_NODE', true),
            array('ROLE_ADMIN', false),
            array('ROLE_USER', false),
            array('ROLE_FROM_PUBLISHED_TO_DRAFT', false),
        );
    }

    /**
     * @param int    $expectedVoterResponse
     * @param string $nodeId
     * @param string $ngrNodeId
     * @param string $ngrRole
     *
     * @dataProvider provideResponseAndNodeData
     */
    public function testVote($expectedVoterResponse, $nodeId, $ngrNodeId, $ngrRole, $ngrIsGranted, $groupSiteId = 'siteId')
    {
        $siteId = 'siteId';
        $role = TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE;
        $node = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        Phake::when($node)->getNodeId()->thenReturn($nodeId);
        Phake::when($node)->getSiteId()->thenReturn($siteId);

        $nodeGroupRole = Phake::mock('OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface');
        Phake::when($nodeGroupRole)->getNodeId()->thenReturn($ngrNodeId);
        Phake::when($nodeGroupRole)->getRole()->thenReturn($ngrRole);
        Phake::when($nodeGroupRole)->isGranted()->thenReturn($ngrIsGranted);

        $group = $this->generateGroupWithSite($groupSiteId);
        Phake::when($group)->getNodeRoleByNodeAndRole($ngrNodeId, $ngrRole)->thenReturn($nodeGroupRole);
        $otherGroup = $this->generateGroupWithSite('otherSiteId');

        $user = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($user)->getGroups()->thenReturn(array($otherGroup, $group));
        $token = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        Phake::when($token)->getUser()->thenReturn($user);

        $this->assertSame($expectedVoterResponse, $this->voter->vote($token, $node, array($role)));
    }

    /**
     * @return array
     */
    public function provideResponseAndNodeData()
    {
        return array(
            array(VoterInterface::ACCESS_GRANTED, 'nodeId', 'nodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, true),
            array(VoterInterface::ACCESS_DENIED, 'nodeId', 'otherNodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, true),
            array(VoterInterface::ACCESS_DENIED, 'nodeId', 'nodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, false),
            array(VoterInterface::ACCESS_DENIED, 'nodeId', 'otherNodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, false),
            array(VoterInterface::ACCESS_DENIED, 'nodeId', 'nodeId', TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE, true),
            array(VoterInterface::ACCESS_DENIED, 'nodeId', 'otherNodeId', TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE, true),
            array(VoterInterface::ACCESS_DENIED, 'nodeId', 'nodeId', TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE, false),
            array(VoterInterface::ACCESS_DENIED, 'nodeId', 'otherNodeId', TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE, false),
            array(VoterInterface::ACCESS_ABSTAIN, 'nodeId', 'nodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, true, 'fakeSiteId'),
            array(VoterInterface::ACCESS_ABSTAIN, 'nodeId', 'otherNodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, true, 'fakeSiteId'),
            array(VoterInterface::ACCESS_ABSTAIN, 'nodeId', 'nodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, false, 'fakeSiteId'),
            array(VoterInterface::ACCESS_ABSTAIN, 'nodeId', 'otherNodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, false, 'fakeSiteId'),
        );
    }

    /**
     * @param string $siteId
     *
     * @return mixed
     */
    protected function generateGroupWithSite($siteId)
    {
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\ReadSiteInterface');
        Phake::when($site)->getSiteId()->thenReturn($siteId);
        $group = Phake::mock('OpenOrchestra\BackofficeBundle\Model\GroupInterface');
        Phake::when($group)->getSite()->thenReturn($site);
        return $group;
    }
}
