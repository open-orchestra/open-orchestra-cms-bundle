<?php

namespace OpenOrchestra\Backoffice\Tests\RestoreEntity;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\Backoffice\RestoreEntity\Strategies\RestoreContentStrategy;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class RestoreContentStrategyTest
 */
class RestoreContentStrategyTest extends AbstractBaseTestCase
{
    /**
     * @var RestoreContentStrategy
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

        $this->strategy = new RestoreContentStrategy($this->contentRepository, $this->eventDispatcher);
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
     * Test restore
     */
    public function testRestore()
    {
        $contentId = 'contentId';
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content)->getContentId()->thenReturn($contentId);
        $contents = new ArrayCollection();
        $contents->add($content);
        $contents->add($content);

        Phake::when($this->contentRepository)->findByContentId($contentId)->thenReturn($contents);

        $this->strategy->restore($content);

        Phake::verify($content, Phake::times(2))->setDeleted(false);
        Phake::verify($this->contentRepository)->findByContentId($contentId);
        Phake::verify($this->eventDispatcher, Phake::times(1))->dispatch(Phake::anyParameters());
    }
}
