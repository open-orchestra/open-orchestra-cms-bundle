<?php

namespace OpenOrchestra\BackofficeBundle\Tests\RestoreEntity;

use OpenOrchestra\Backoffice\RestoreEntity\Strategies\RestoreContentStrategy;
use Phake;

/**
 * Class RestoreContentStrategyTest
 */
class RestoreContentStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RestoreContentStrategy
     */
    protected $strategy;

    protected $contentManager;

    /**
     * Set up the test
     */
    protected function setUp()
    {
        $this->contentManager = Phake::mock('OpenOrchestra\BackofficeBundle\Manager\ContentManager');

        $this->strategy = new RestoreContentStrategy($this->contentManager);
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
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        $this->strategy->restore($content);

        Phake::verify($this->contentManager)->restoreContent($content);
    }
}
