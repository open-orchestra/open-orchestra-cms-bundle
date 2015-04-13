<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use Phake;
use OpenOrchestra\ApiBundle\Transformer\GenerateBlockTransformer;

/**
 * Class GenerateBlockTransformerTest
 */
class GenerateBlockTransformerTest extends \PHPUnit_Framework_TestCase
{
    protected $displayIconManager;
    protected $generateBlockTransformer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->displayIconManager = Phake::mock('OpenOrchestra\BackofficeBundle\DisplayIcon\DisplayManager');
        $this->generateBlockTransformer = new GenerateBlockTransformer($this->displayIconManager);
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $name = $this->generateBlockTransformer->getName();

        $this->assertSame('generate_block', $name);
    }

    /**
     * Test transform
     */
    public function testTransform()
    {
        $name = 'fakeName';
        $icon = 'fakeIcon';

        Phake::when($this->displayIconManager)->show($name)->thenReturn($icon);
        $facadeExcepted = $this->generateBlockTransformer->transform($name);

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\GenerateBlockFacade', $facadeExcepted);
        $this->assertSame($name, $facadeExcepted->name);
        $this->assertSame($icon, $facadeExcepted->icon);
    }
}
