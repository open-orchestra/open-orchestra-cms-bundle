<?php

namespace OpenOrchestra\Backoffice\Tests\RestoreEntity;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\Backoffice\DeleteTrashcanEntity\Strategies\DeleteTrashCanNodeStrategy;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class DeleteTrashcanContentStrategyTest
 */
class DeleteTrashcanNodeStrategyTest extends AbstractBaseTestCase
{
    /**
     * @var DeleteTrashCanNodeStrategy
     */
    protected $strategy;

    protected $nodeRepository;
    protected $trashItemRepository;
    protected $eventDispatcher;
    protected $objectManager;

    /**
     * Set up the test
     */
    protected function setUp()
    {
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $this->trashItemRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\TrashItemRepositoryInterface');
        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');

        $this->strategy = new DeleteTrashCanNodeStrategy($this->nodeRepository, $this->trashItemRepository, $this->eventDispatcher, $this->objectManager);
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
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');

        return array(
            array($node, true),
            array($content, false),
            array($site, false),
        );
    }

    /**
     * Test delete
     */
    public function testDeleteOnlyOneNode()
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->nodeRepository)->findByNodeAndSite(Phake::anyParameters())->thenReturn(array());

        $this->strategy->delete($node);

        Phake::verify($this->objectManager)->remove($node);
        Phake::verify($this->eventDispatcher)->dispatch(Phake::anyParameters());
        Phake::verify($this->objectManager)->flush();
    }

    /**
     * Test delete
     */
    public function testDeleteWithSubNode()
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->isDeleted()->thenReturn(true);
        $subNode1 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($subNode1)->isDeleted()->thenReturn(true);
        $subNode2 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($subNode2)->isDeleted()->thenReturn(true);

        $trashItem = Phake::mock('OpenOrchestra\ModelInterface\Model\TrashItemInterface');
        Phake::when($this->trashItemRepository)->findByEntity(Phake::anyParameters())->thenReturn($trashItem);

        Phake::when($this->nodeRepository)->findByNodeAndSite(Phake::anyParameters())->thenReturn(array($node));
        Phake::when($this->nodeRepository)->findByIncludedPathAndSiteId(Phake::anyParameters())->thenReturn(array($node, $subNode1, $subNode2));

        $this->strategy->delete($node);

        Phake::verify($this->objectManager, Phake::times(6))->remove(Phake::anyParameters());
        Phake::verify($this->eventDispatcher, Phake::times(3))->dispatch(Phake::anyParameters());
        Phake::verify($this->objectManager)->flush();
    }
}
