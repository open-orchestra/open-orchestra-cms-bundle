<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface;
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
    protected $nodeRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->voter = new NodeGroupRoleVoter($this->nodeRepository);
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
     * @param string $ngrAccessType
     * @param string $groupSiteId
     *
     * @dataProvider provideResponseAndNodeData
     */
    public function testVote($expectedVoterResponse, $nodeId, $ngrNodeId, $ngrRole, $ngrAccessType, $groupSiteId = 'siteId')
    {
        $siteId = 'siteId';
        $role = TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE;
        $node = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        Phake::when($node)->getNodeId()->thenReturn($nodeId);
        Phake::when($node)->getSiteId()->thenReturn($siteId);

        $nodeGroupRole = Phake::mock('OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface');
        Phake::when($nodeGroupRole)->getAccessType()->thenReturn($ngrAccessType);

        $group = $this->generateGroup($groupSiteId);
        Phake::when($group)->getNodeRoleByNodeAndRole($ngrNodeId, $ngrRole)->thenReturn($nodeGroupRole);
        $otherGroup = $this->generateGroup('otherSiteId');
        $noSiteGroup = $this->generateGroup();

        $user = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($user)->getGroups()->thenReturn(array($noSiteGroup, $otherGroup, $group));
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
            array(VoterInterface::ACCESS_GRANTED, 'nodeId', 'nodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, NodeGroupRoleInterface::ACCESS_GRANTED),
            array(VoterInterface::ACCESS_DENIED, 'nodeId', 'otherNodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, NodeGroupRoleInterface::ACCESS_GRANTED),
            array(VoterInterface::ACCESS_DENIED, 'nodeId', 'nodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, NodeGroupRoleInterface::ACCESS_DENIED),
            array(VoterInterface::ACCESS_DENIED, 'nodeId', 'otherNodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, NodeGroupRoleInterface::ACCESS_DENIED),
            array(VoterInterface::ACCESS_DENIED, 'nodeId', 'nodeId', TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE, NodeGroupRoleInterface::ACCESS_GRANTED),
            array(VoterInterface::ACCESS_DENIED, 'nodeId', 'otherNodeId', TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE, NodeGroupRoleInterface::ACCESS_GRANTED),
            array(VoterInterface::ACCESS_DENIED, 'nodeId', 'nodeId', TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE, NodeGroupRoleInterface::ACCESS_DENIED),
            array(VoterInterface::ACCESS_DENIED, 'nodeId', 'otherNodeId', TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE, NodeGroupRoleInterface::ACCESS_DENIED),
            array(VoterInterface::ACCESS_ABSTAIN, 'nodeId', 'nodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, NodeGroupRoleInterface::ACCESS_GRANTED, 'fakeSiteId'),
            array(VoterInterface::ACCESS_ABSTAIN, 'nodeId', 'otherNodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE,NodeGroupRoleInterface::ACCESS_GRANTED, 'fakeSiteId'),
            array(VoterInterface::ACCESS_ABSTAIN, 'nodeId', 'nodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, NodeGroupRoleInterface::ACCESS_DENIED, 'fakeSiteId'),
            array(VoterInterface::ACCESS_ABSTAIN, 'nodeId', 'otherNodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, NodeGroupRoleInterface::ACCESS_DENIED, 'fakeSiteId'),
            array(VoterInterface::ACCESS_ABSTAIN, 'nodeId', 'nodeId', '5640af7a02b0cf39178b4598', NodeGroupRoleInterface::ACCESS_DENIED, 'fakeSiteId'),
        );
    }

    /**
     * @param int    $expectedVoterResponse
     * @param string $nodeId
     * @param string $ngrNodeId
     * @param string $ngrRole
     * @param string $ngrParentAccessType
     * @param string $groupSiteId
     *
     * @dataProvider provideResponseAndNodeDataInherit
     */
    public function testVoteInherit($expectedVoterResponse, $nodeId,  $ngrNodeId, $ngrRole, $ngrParentAccessType, $groupSiteId = 'siteId')
    {
        $siteId = 'siteId';
        $language = 'fakeLanguage';
        $parentId = 'parentId';
        $role = TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE;

        $node = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        Phake::when($node)->getNodeId()->thenReturn($nodeId);
        Phake::when($node)->getSiteId()->thenReturn($siteId);
        Phake::when($node)->getLanguage()->thenReturn($language);
        Phake::when($node)->getParentId()->thenReturn($parentId);

        $parentNode = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        Phake::when($parentNode)->getNodeId()->thenReturn($parentId);

        Phake::when($this->nodeRepository)->findInLastVersion(Phake::anyParameters())->thenReturn($parentNode);

        $nodeGroupRole = Phake::mock('OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface');
        Phake::when($nodeGroupRole)->getAccessType()->thenReturn(NodeGroupRoleInterface::ACCESS_INHERIT);

        $parentNodeGroupRole = Phake::mock('OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface');
        Phake::when($parentNodeGroupRole)->getAccessType()->thenReturn($ngrParentAccessType);

        $group = $this->generateGroup($groupSiteId);
        Phake::when($group)->getNodeRoleByNodeAndRole($ngrNodeId, $ngrRole)->thenReturn($nodeGroupRole);
        Phake::when($group)->getNodeRoleByNodeAndRole($parentId, $ngrRole)->thenReturn($parentNodeGroupRole);
        $otherGroup = $this->generateGroup('otherSiteId');
        $noSiteGroup = $this->generateGroup();

        $user = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($user)->getGroups()->thenReturn(array($noSiteGroup, $otherGroup, $group));
        $token = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        Phake::when($token)->getUser()->thenReturn($user);

        $this->assertSame($expectedVoterResponse, $this->voter->vote($token, $node, array($role)));
    }

    /**
     * @return array
     */
    public function provideResponseAndNodeDataInherit()
    {
        return array(
            array(VoterInterface::ACCESS_GRANTED, 'nodeId', 'nodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, NodeGroupRoleInterface::ACCESS_GRANTED),
            array(VoterInterface::ACCESS_DENIED, 'nodeId', 'nodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, NodeGroupRoleInterface::ACCESS_DENIED),
            array(VoterInterface::ACCESS_DENIED, 'nodeId', 'otherNodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, NodeGroupRoleInterface::ACCESS_GRANTED),
            array(VoterInterface::ACCESS_DENIED, 'nodeId', 'otherNodeId', TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE, NodeGroupRoleInterface::ACCESS_GRANTED),
            array(VoterInterface::ACCESS_DENIED, 'nodeId', 'nodeId', TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE, NodeGroupRoleInterface::ACCESS_DENIED),
            array(VoterInterface::ACCESS_DENIED, 'nodeId', 'otherNodeId', TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE, NodeGroupRoleInterface::ACCESS_DENIED),
            array(VoterInterface::ACCESS_ABSTAIN, 'nodeId', 'nodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, NodeGroupRoleInterface::ACCESS_GRANTED, 'fakeSiteId'),
            array(VoterInterface::ACCESS_ABSTAIN, 'nodeId', 'otherNodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, NodeGroupRoleInterface::ACCESS_GRANTED, 'fakeSiteId'),
            array(VoterInterface::ACCESS_ABSTAIN, 'nodeId', 'nodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, NodeGroupRoleInterface::ACCESS_DENIED, 'fakeSiteId'),
            array(VoterInterface::ACCESS_ABSTAIN, 'nodeId', 'otherNodeId', TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE, NodeGroupRoleInterface::ACCESS_DENIED, 'fakeSiteId'),
            array(VoterInterface::ACCESS_ABSTAIN, 'nodeId', 'nodeId', '5640af7a02b0cf39178b4598', NodeGroupRoleInterface::ACCESS_DENIED, 'fakeSiteId'),
        );
    }

    /**
     * @param string $siteId
     *
     * @return mixed
     */
    protected function generateGroup($siteId = null)
    {
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\ReadSiteInterface');
        Phake::when($site)->getSiteId()->thenReturn($siteId);
        $group = Phake::mock('OpenOrchestra\BackofficeBundle\Model\GroupInterface');
        if (!is_null($siteId)){
            Phake::when($group)->getSite()->thenReturn($site);
        }

        return $group;
    }
}
