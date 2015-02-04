<?php

namespace PHPOrchestra\LogBundle\Test\Transformer;

use Phake;
use PHPOrchestra\LogBundle\Transformer\LogTransformer;

/**
 * Class LogTransformerTest
 */
class LogTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogTransformer
     */
    protected $transformer;

    protected $log;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->log = Phake::mock('PHPOrchestra\LogBundle\Model\LogInterface');
        $this->transformer = new LogTransformer();
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $this->assertSame($this->transformer->getName(), 'log');
    }

    /**
     * Test transform
     */
    public function testTransform()
    {
        $facade = $this->transformer->transform($this->log);

        $this->assertInstanceOf('PHPOrchestra\LogBundle\Facade\LogFacade', $facade);
    }
}
