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
    protected $translator;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        $this->log = Phake::mock('PHPOrchestra\LogBundle\Model\LogInterface');
        $this->transformer = new LogTransformer($this->translator);
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
        Phake::when($this->log)->getContext()->thenReturn(array());
        $facade = $this->transformer->transform($this->log);

        Phake::verify($this->translator)->trans(Phake::anyParameters());
        $this->assertInstanceOf('PHPOrchestra\LogBundle\Facade\LogFacade', $facade);
    }
}
