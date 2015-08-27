<?php

namespace OpenOrchestra\BackofficeBundle\Tests\RestoreEntity;

use OpenOrchestra\Backoffice\RestoreEntity\Strategies\RestoreNodeStrategy;
use Phake;

/**
 * Class RestoreNodeStrategyTest
 */
class RestoreNodeStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RestoreNodeStrategy
     */
    protected $strategy;

    protected $nodeManager;

    /**
     * Set up the test
     */
    protected function setUp()
    {
        $this->nodeManager = Phake::mock('OpenOrchestra\BackofficeBundle\Manager\NodeManager');

        $this->strategy = new RestoreNodeStrategy($this->nodeManager);
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
     * Test restore
     */
    public function testRestore()
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $this->strategy->restore($node);

        Phake::verify($this->nodeManager)->restoreNode($node);
    }
}
