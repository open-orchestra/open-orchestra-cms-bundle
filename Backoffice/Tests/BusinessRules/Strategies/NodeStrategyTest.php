<?php
namespace OpenOrchestra\BackOffice\Tests\BusinessRules\Strategies;

use OpenOrchestra\Backoffice\BusinessRules\Strategies\BusinessActionInterface;
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
    protected $strategy;
    protected $mainUrlArguments = array(
        'mainUrlArgument0',
        'mainUrlArgument1'
    );

    /**
     * setUp
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->generateFormManager = Phake::mock('OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager');
        Phake::when($this->generateFormManager)->getRequiredUriParameter(Phake::anyParameters())->thenReturn($this->mainUrlArguments);
        $this->strategy = new NodeStrategy($this->nodeRepository, $this->generateFormManager);
    }

    /**
     * @param NodeInterface $node
     * @param boolean       $isGranted
     *
     * @dataProvider provideEditNode
     */
    public function testCanEdit(NodeInterface $node, $isGranted)
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
     * @param NodeInterface $node
     * @param boolean       $isWithoutAutoUnpublishToState
     * @param int           $countByParentId
     * @param boolean       $isGranted
     *
     * @dataProvider provideDeleteNode
     */
    public function testCanDelete(NodeInterface $node, $isWithoutAutoUnpublishToState, $countByParentId, $isGranted)
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
     * @param NodeInterface $node
     * @param int           $nbrVersions
     * @param boolean       $isGranted
     *
     * @dataProvider provideDeleteVersionNode
     */
    public function testCanDeleteVersion(NodeInterface $node, $nbrVersions, $isGranted)
    {
        Phake::when($this->nodeRepository)->countNotDeletedVersions(Phake::anyParameters())->thenReturn($nbrVersions);
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
            array($node0, 1, false),
            array($node0, 2, true),
            array($node1, 2, false),
        );
    }

    /**
     * @param NodeInterface $node
     * @param boolean       $isGranted
     *
     * @dataProvider provideCanChangeToPublishStatus
     */
    public function testCanChangeToPublishStatus(NodeInterface $node, $isGranted)
    {
        $this->assertSame($isGranted, $this->strategy->canChangeToPublishStatus($node, array()));
    }

    /**
     * provide group and parameters
     *
     * @return array
     */
    public function provideCanChangeToPublishStatus()
    {
        $block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        $area = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($area)->getBlocks()->thenReturn(array($block));
        $node0 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node0)->getAreas()->thenReturn(array($area));
        Phake::when($node0)->getRoutePattern()->thenReturn('');


        $node1 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node1)->getAreas()->thenReturn(array($area));
        Phake::when($node1)->getRoutePattern()->thenReturn('/{mainUrlArgument0}/{mainUrlArgument1}');

        return array(
            array($node0, false),
            array($node1, true),
        );
    }

    /**
     * test getActions
     */
    public function testGetActions()
    {
        $this->assertEquals(array(
            BusinessActionInterface::DELETE => 'canDelete',
            NodeStrategy::DELETE_VERSION => 'canDeleteVersion',
            BusinessActionInterface::EDIT => 'canEdit',
            NodeStrategy::CHANGE_TO_PUBLISH_STATUS => 'canChangeToPublishStatus',
            NodeStrategy::CHANGE_STATUS => 'canChangeStatus',
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
