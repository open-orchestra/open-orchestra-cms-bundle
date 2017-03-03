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

    /**
     * Set up the test
     */
    protected function setUp()
    {
        $this->contentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');
        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

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
        /*$contentDe = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        $contentFr = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        $contentEn = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        $contents = array($contentDe, $contentFr, $contentEn);

        Phake::when($this->contentRepository)->findByContentId(Phake::anyParameters())->thenReturn($contents);

        $this->strategy->remove($contentFr);
        foreach ($contents as $content) {
            Phake::verify($this->objectManager)->remove($content);
        }
        Phake::verify($this->eventDispatcher, Phake::times(count($contents)))->dispatch(Phake::anyParameters());
        Phake::verify($this->objectManager)->flush();*/
    }
}
