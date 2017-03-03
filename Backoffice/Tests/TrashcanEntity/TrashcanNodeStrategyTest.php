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

    /**
     * Set up the test
     */
    protected function setUp()
    {
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->nodeManager = Phake::mock('OpenOrchestra\Backoffice\Manager\NodeManager');

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
    public function testRemoveOnlyOneNode()
    {
        /*$node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->nodeRepository)->findByNodeAndSite(Phake::anyParameters())->thenReturn(array());

        $this->strategy->remove($node);

        Phake::verify($this->objectManager)->remove($node);
        Phake::verify($this->eventDispatcher)->dispatch(Phake::anyParameters());
        Phake::verify($this->objectManager)->flush();*/
    }
}
