<?php

namespace OpenOrchestra\WysibbBundle\Tests\Twig;

use Phake;
use OpenOrchestra\WysibbBundle\Twig\WysibbExtension;

/**
 * Class WysibbExtensionTest
 */
class WysibbExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WysibbExtension
     */
    protected $extension;

    protected $container;
    protected $templating;
    protected $requestStack;
    protected $masterRequest;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->templating = Phake::mock('Symfony\Component\Templating\EngineInterface');
        $this->requestStack = Phake::mock('Symfony\Component\HttpFoundation\RequestStack');
        $this->masterRequest = Phake::mock('Symfony\Component\HttpFoundation\Request');
        $this->container = Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        Phake::when($this->container)->get('templating')->thenReturn($this->templating);
        Phake::when($this->container)->get('request_stack')->thenReturn($this->requestStack);
        Phake::when($this->requestStack)->getMasterRequest()->thenReturn($this->masterRequest);

        $this->extension = new WysibbExtension();
        $this->extension->setContainer($this->container);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Twig_Extension', $this->extension);
    }

    /**
     * Test name
     */
    public function testGetName()
    {
        $this->assertSame('wysibb', $this->extension->getName());
    }

    /**
     * Test functions declared
     */
    public function testGetFunctions()
    {
        $twigNode = Phake::mock('Twig_Node');
        $functions = $this->extension->getFunctions();

        $this->assertCount(1, $functions);
        $function = $functions[0];
        $this->assertInstanceOf('Twig_SimpleFunction', $function);
        $this->assertSame('wysibb_init', $function->getName());
        $this->assertTrue(is_callable($function->getCallable()));
        $this->assertSame(array('html'), $function->getSafe($twigNode));
    }

    /**
     * @param array  $config
     * @param array  $translations
     * @param string $locale
     *
     * @dataProvider provideWysibbInit
     */
    public function testWysibbInit(array $config,array $translations, $locale)
    {
        Phake::when($this->container)->getParameter('open_orchestra_wysibb.config')->thenReturn($config);
        Phake::when($this->container)->getParameter('open_orchestra_wysibb.translations')->thenReturn($translations);
        Phake::when($this->masterRequest)->getLocale()->thenReturn($locale);

        $this->extension->wysibbInit();

        Phake::verify($this->templating)->render('OpenOrchestraWysibbBundle:Script:init.html.twig', array(
            'wysibb_config' => json_encode($config),
            'wysibb_translations' => json_encode($translations),
            'locale' => $locale,
        ));
        Phake::verify($this->masterRequest)->getLocale();
    }

    /**
     * @return array
     */
    public function provideWysibbInit()
    {
        return array(
            array(
                array(
                    "buttons" => "bold,italic,underline,|,img,link,|,code,quote,quote,quote"),
                array(
                    "en" => array(
                        "bold" => "Bold",
                        "video" => "Insert Video"),
                    "fr" => array(
                        "bold" => "Gras",
                        "video" => "Insérer vidéo Youtube")
                ),
                "fr"
            ),
            array(array(), array(), 'en'),
        );
    }

}
