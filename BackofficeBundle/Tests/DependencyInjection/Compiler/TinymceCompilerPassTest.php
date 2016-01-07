<?php

namespace OpenOrchestra\BackofficeBundle\Tests\DependencyInjection\Compiler;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\BackofficeBundle\DependencyInjection\Compiler\TinymceCompilerPass;

/**
 * Class TinymceCompilerPassTest
 */
class TinymceCompilerPassTest extends AbstractBaseTestCase
{
    /**
     * @var TinymceCompilerPass
     */
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
        Phake::when($this->container)->getParameter('stfalcon_tinymce.config')->thenReturn($param);

        $this->compiler->process($this->container);

        Phake::verify($this->container)->getParameter('stfalcon_tinymce.config');
        Phake::verify($this->container)->setParameter('stfalcon_tinymce.config', $param);
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

        Phake::verify($this->container, Phake::never())->getParameter('stfalcon_tinymce.config');
        Phake::verify($this->container, Phake::never())->setParameter('stfalcon_tinymce.config', $param);
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
                    'selector' => ".tinymce",
                    'theme' => array(
                        'simple' => array(
                            "theme"        => "modern",
                            "plugins"      => array(
                                "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                                "searchreplace wordcount visualblocks visualchars code fullscreen",
                                "insertdatetime media nonbreaking save table contextmenu directionality",
                                "emoticons template paste textcolor"
                            ),
                            "toolbar1"     => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link",
                            "toolbar2"     => "print preview | forecolor backcolor",
                            "menubar"      => false,
                        )
                    )
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

    /**
     * Test parameter addition
     */
    public function testWithDefinitionAndOpenOrchestraParameters()
    {
        Phake::when($this->container)->hasDefinition(Phake::anyParameters())->thenReturn(true);
        Phake::when($this->container)->getParameter('stfalcon_tinymce.config')->thenReturn(array());
        Phake::when($this->container)->hasParameter(Phake::anyParameters())->thenReturn(true);
        Phake::when($this->container)->getParameter('open_orchestra_backoffice.tinymce')->thenReturn(array(
            'content_css' => 'testcontent',
        ));
        Phake::when($this->container)->getParameter('router.request_context.host')->thenReturn('foo.host');

        $this->compiler->process($this->container);

        Phake::verify($this->container)->setParameter('stfalcon_tinymce.config', array('content_css' => 'foo.hosttestcontent'));
    }
}
