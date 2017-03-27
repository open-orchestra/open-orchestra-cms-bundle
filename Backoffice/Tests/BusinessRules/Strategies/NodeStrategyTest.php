<?php
namespace OpenOrchestra\BackOffice\Tests\BusinessRules\Strategies;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Phake;
use OpenOrchestra\Backoffice\BusinessRules\Strategies\NodeStrategy;

/**
 * Class NodeStrategyTest
 */
class NodeStrategyTest extends AbstractBaseTestCase
{
    protected $nodeRepository;
    protected $contextManeger;
    protected $strategy;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->contextManeger = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');

        Phake::when($this->contextManeger)->getCurrentSiteId()->thenReturn('fakeSiteId');

        $this->strategy = new NodeStrategy(
            $this->nodeRepository,
            $this->contextManeger);
    }

    /**
     * @param int            $node
     * @param boolean        $isGranted
     *
     * @dataProvider provideEditNode
     */
    public function testCanEdit($node, $isGranted)
    {
        $this->assertSame($isGranted, $this->strategy->canEdit($node, array()));
    }

    /**
     * provide group and parameters
     *
     * @return array
     */
    public function provideEditNode()
    {
        $status0 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status0)->isBlockedEdition()->thenReturn(false);
        $node0 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node0)->getStatus()->thenReturn($status0);

        $status1 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status1)->isBlockedEdition()->thenReturn(true);
        $node1 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node1)->getStatus()->thenReturn($status1);

        return array(
            array($node0, true),
            array($node1, false),
        );
    }

    /**
     * @param int     $node
     * @param boolean $isWithoutAutoUnpublishToState
     * @param int     $countByParentId
     * @param boolean $isGranted
     *
     * @dataProvider provideDeleteNode
     */
    public function testCanDelete($node, $isWithoutAutoUnpublishToState, $countByParentId, $isGranted)
    {
        Phake::when($this->nodeRepository)->hasNodeIdWithoutAutoUnpublishToState(Phake::anyParameters())->thenReturn($isWithoutAutoUnpublishToState);
        Phake::when($this->nodeRepository)->countByParentId(Phake::anyParameters())->thenReturn($countByParentId);

        $this->assertSame($isGranted, $this->strategy->canDelete($node, array()));
    }

    /**
     * provide group and parameters
     *
     * @return array
     */
    public function provideDeleteNode()
    {
        $node0 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node0)->getNodeId()->thenReturn(NodeInterface::ROOT_NODE_ID);

        $node1 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node1)->getNodeId()->thenReturn('fakeNodeId1');

        return array(
            array($node0, true, 0, false),
            array($node0, true, 1, false),
            array($node0, false, 0, false),
            array($node0, false, 1, false),
            array($node1, true, 0, false),
            array($node1, true, 1, false),
            array($node1, false, 0, true),
            array($node1, false, 1, false),
        );
    }

    /**
     * @param int            $node
     * @param boolean        $isGranted
     *
     * @dataProvider provideDeleteVersionNode
     */
    public function testCanDeleteVersion($node, $isGranted)
    {
        $this->assertSame($isGranted, $this->strategy->canDeleteVersion($node, array()));
    }

    /**
     * provide group and parameters
     *
     * @return array
     */
    public function provideDeleteVersionNode()
    {
        $status0 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status0)->isPublishedState()->thenReturn(false);
        $node0 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node0)->getStatus()->thenReturn($status0);

        $status1 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status1)->isPublishedState()->thenReturn(true);
        $node1 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node1)->getStatus()->thenReturn($status1);

        return array(
            array($node0, true),
            array($node1, false),
        );
    }

    /**
     * test getActions
     */
    public function testGetActions()
    {
        $this->assertEquals(array(
            ContributionActionInterface::DELETE => 'canDelete',
            NodeStrategy::DELETE_VERSION => 'canDeleteVersion',
            ContributionActionInterface::EDIT => 'canEdit',
        ), $this->strategy->getActions());
    }

    /**
     * test getActions
     */
    public function testType()
    {
        $this->assertEquals(NodeInterface::ENTITY_TYPE, $this->strategy->getType());
    }
}
