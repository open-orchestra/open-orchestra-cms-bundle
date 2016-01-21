<?php

namespace OpenOrchestra\BackofficeBundle\Tests\DependencyInjection\Compiler;

use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\FieldToElasticaTypeCompilerPass;
use Phake;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Test FieldToElasticaTypeCompilerPassTest
 */
class FieldToElasticaTypeCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FieldToElasticaTypeCompilerPass
     */
    protected $compiler;

    protected $containerBuilder;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->containerBuilder = Phake::mock(ContainerBuilder::CLASS);

        $this->compiler = new FieldToElasticaTypeCompilerPass();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(CompilerPassInterface::CLASS, $this->compiler);
    }

    /**
     * Test with no mapper
     */
    public function testProcessWithNoMapper()
    {
        $this->compiler->process($this->containerBuilder);

        Phake::verify($this->containerBuilder)->has('open_orchestra_elastica.mapper.form');
    }

    /**
     * Test with mapper present
     */
    public function testProcessWithMapper()
    {
        $definition = Phake::mock(Definition::CLASS);
        Phake::when($this->containerBuilder)->getDefinition(Phake::anyParameters())->thenReturn($definition);
        Phake::when($this->containerBuilder)->has(Phake::anyParameters())->thenReturn(true);

        $this->compiler->process($this->containerBuilder);

        Phake::verify($definition)->addMethodCall('addMappingConfiguration', array('date', 'date'));
        Phake::verify($definition)->addMethodCall('addMappingConfiguration', array('integer', 'double'));
        Phake::verify($definition)->addMethodCall('addMappingConfiguration', array('embedded_content', 'object'));
    }
}
