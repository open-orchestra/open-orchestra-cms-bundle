<?php

namespace OpenOrchestra\Backoffice\Tests\RemoveTrashcanEntity;

use OpenOrchestra\Backoffice\TrashcanEntity\Strategies\TrashCanNodeStrategy;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class TrashcanNodeStrategyTest
 */
class TrashcanNodeStrategyTest extends AbstractBaseTestCase
{
    /**
     * @var TrashCanNodeStrategy
     */
    protected $strategy;

    protected $nodeRepository;
    protected $eventDispatcher;
    protected $nodeManager;
    protected $trashItem;
    protected $nodeId;

    /**
     * Set up the test
     */
    protected function setUp()
    {
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->nodeManager = Phake::mock('OpenOrchestra\Backoffice\Manager\NodeManager');
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getId()->thenReturn('fake_node_id');

        Phake::when($this->nodeRepository)->findByNodeAndSite(Phake::anyParameters())->thenReturn(array($node));

        $this->trashItem = Phake::mock('OpenOrchestra\ModelInterface\Model\TrashItemInterface');
        Phake::when($this->trashItem)->getEntityId()->thenReturn($this->nodeId);

        $this->strategy = new TrashCanNodeStrategy($this->nodeRepository, $this->eventDispatcher, $this->nodeManager);
    }

    /**
     * @param mixed  $entity
     * @param bool   $expected
     *
     * @dataProvider provideSupport
     */
    public function testSupport($entity, $expected)
    {
        $output = $this->strategy->support($entity);
        $this->assertEquals($output, $expected);
    }

    /**
     * @return array
     */
    public function provideSupport()
    {
        $trashItemNode = Phake::mock('OpenOrchestra\ModelInterface\Model\TrashItemInterface');
        Phake::when($trashItemNode)->getType()->thenReturn('node');

        $trashItemContent = Phake::mock('OpenOrchestra\ModelInterface\Model\TrashItemInterface');
        Phake::when($trashItemContent)->getType()->thenReturn('content');

        return array(
            array($trashItemNode, true),
            array($trashItemContent, false),
        );
    }

    /**
     * Test remove
     */
    public function testRemove()
    {
        $this->strategy->remove($this->trashItem);

        Phake::verify($this->eventDispatcher, Phake::times(2))->dispatch(Phake::anyParameters());
        Phake::verify($this->nodeManager)->deleteBlockInNode(Phake::anyParameters());
        Phake::verify($this->nodeRepository)->removeNodeVersions(array('fake_node_id'));
    }

    /**
     * Test restore
     */
    public function testRestore()
    {
        $this->strategy->restore($this->trashItem);

        Phake::verify($this->eventDispatcher)->dispatch(Phake::anyParameters());
        Phake::verify($this->nodeRepository)->restoreDeletedNode(Phake::anyParameters());
    }
}
