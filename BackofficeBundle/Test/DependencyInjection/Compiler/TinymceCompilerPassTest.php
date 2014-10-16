<?php

namespace PHPOrchestra\BackofficeBundle\Test\DependencyInjection\Compiler;

use Phake;
use PHPOrchestra\BackofficeBundle\DependencyInjection\Compiler\TinymceCompilerPass;

/**
 * Class TinymceCompilerPassTest
 */
class TinymceCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    protected $compiler;
    protected $tinymce;
    protected $container;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->tinymce = Phake::mock('Stfalcon\Bundle\TinymceBundle\DependencyInjection\StfalconTinymceExtension');
        $this->container = Phake::mock('Symfony\Component\DependencyInjection\ContainerBuilder');
        Phake::when($this->container)->getDefinition(Phake::anyParameters())->thenReturn($this->tinymce);

        $this->compiler = new TinymceCompilerPass();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface', $this->compiler);
    }

    /**
     * Test with definition
     *
     * @param array $param
     *
     * @dataProvider parameterWithDefProvider
     */
    public function testWithDefinition($param)
    {
        Phake::when($this->container)->hasDefinition(Phake::anyParameters())->thenReturn(true);
        Phake::when($this->container)->getDefinition(Phake::anyParameters())->thenReturn($this->tinymce);

        $this->compiler->process($this->container);

        Phake::verify($this->container)->setParameter($this->tinymce, $param);
    }

    /**
     * Test with definition
     *
     * @param array $param
     *
     * @dataProvider parameterNoDefProvider
     */
    public function testNoDefinition($param)
    {
        Phake::when($this->container)->hasDefinition(Phake::anyParameters())->thenReturn(false);

        $this->compiler->process($this->container);

        Phake::verify($this->container, Phake::never())->getDefinition(Phake::anyParameters());
        Phake::verify($this->container, Phake::never())->setParameter($this->tinymce, $param);
    }

    /**
     * @return array
     */
    public function parameterWithDefProvider()
    {
        return array(
            array(
                array(
                    'tinymce_jquery' => false,
                    'include_jquery' => false,
                    'selector' => ".tinymce"
                )
            ),
        );
    }

    /**
     * @return array
     */
    public function parameterNoDefProvider()
    {
        return array(
            array(array('tinymce_jquery' => false)),
            array(
                array(
                    'tinymce_jquery' => false,
                    'include_jquery' => false
                )
            ),
            array(
                array(
                    'tinymce_jquery' => false,
                    'include_jquery' => false,
                    'selector' => ".tinymce"
                )
            ),
            array(
                array(
                    'tinymce_jquery' => true,
                    'include_jquery' => false,
                    'selector' => ".tinymce"
                )
            ),
            array(
                array(
                    'tinymce_jquery' => true,
                    'include_jquery' => true,
                    'selector' => ".tinymce"
                )
            ),
        );
    }
}
