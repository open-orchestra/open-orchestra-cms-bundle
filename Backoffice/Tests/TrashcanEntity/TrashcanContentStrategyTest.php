<?php

namespace OpenOrchestra\Backoffice\Tests\TrashcanEntity;

use OpenOrchestra\Backoffice\TrashcanEntity\Strategies\TrashCanContentStrategy;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class TrashcanContentStrategyTest
 */
class TrashcanContentStrategyTest extends AbstractBaseTestCase
{
    /**
     * @var TrashCanContentStrategy
     */
    protected $strategy;

    protected $contentRepository;
    protected $eventDispatcher;
    protected $contents;
    protected $trashItem;
    protected $contentId = 'fake_content_id';

    /**
     * Set up the test
     */
    protected function setUp()
    {
        $this->contentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');
        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $contentDe = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($contentDe)->getId()->thenReturn('content_de');
        $contentFr = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($contentFr)->getId()->thenReturn('content_fr');
        $contentEn = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($contentEn)->getId()->thenReturn('content_en');
        $this->contents = array($contentDe, $contentFr, $contentEn);

        $this->trashItem = Phake::mock('OpenOrchestra\ModelInterface\Model\TrashItemInterface');
        Phake::when($this->trashItem)->getEntityId()->thenReturn($this->contentId);

        Phake::when($this->contentRepository)->findByContentId(Phake::anyParameters())->thenReturn($this->contents);

        $this->strategy = new TrashCanContentStrategy($this->contentRepository, $this->eventDispatcher);
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
            array($trashItemNode, false),
            array($trashItemContent, true),
        );
    }

    /**
     * Test remove
     */
    public function testRemove()
    {
        $this->strategy->remove($this->trashItem);

        Phake::verify($this->eventDispatcher, Phake::times(count($this->contents)))->dispatch(Phake::anyParameters());
        Phake::verify($this->contentRepository)->removeContentVersion(array('content_de', 'content_fr', 'content_en'));
    }

    /**
     * Test restore
     */
    public function testRestore()
    {
        $this->strategy->restore($this->trashItem);
        Phake::verify($this->eventDispatcher, Phake::times(count($this->contents)))->dispatch(Phake::anyParameters());
        Phake::verify($this->contentRepository)->restoreDeletedContent($this->contentId);
    }
}
