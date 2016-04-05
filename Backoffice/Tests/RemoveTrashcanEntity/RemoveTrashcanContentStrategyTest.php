<?php

namespace OpenOrchestra\Backoffice\Tests\RemoveTrashcanEntity;

use OpenOrchestra\Backoffice\RemoveTrashcanEntity\Strategies\RemoveTrashCanContentStrategy;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class RemoveTrashcanContentStrategyTest
 */
class RemoveTrashcanContentStrategyTest extends AbstractBaseTestCase
{
    /**
     * @var RemoveTrashCanContentStrategy
     */
    protected $strategy;

    protected $contentRepository;
    protected $eventDispatcher;
    protected $objectManager;

    /**
     * Set up the test
     */
    protected function setUp()
    {
        $this->contentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');
        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');

        $this->strategy = new RemoveTrashCanContentStrategy($this->contentRepository, $this->eventDispatcher, $this->objectManager);
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
            array($node, false),
            array($content, true),
            array($site, false),
        );
    }

    /**
     * Test remove
     */
    public function testRemove()
    {
        $contentDe = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        $contentFr = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        $contentEn = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        $contents = array($contentDe, $contentFr, $contentEn);

        Phake::when($this->contentRepository)->findByContentId(Phake::anyParameters())->thenReturn($contents);

        $this->strategy->remove($contentFr);
        foreach ($contents as $content) {
            Phake::verify($this->objectManager)->remove($content);
        }
        Phake::verify($this->eventDispatcher, Phake::times(count($contents)))->dispatch(Phake::anyParameters());
        Phake::verify($this->objectManager)->flush();
    }
}
