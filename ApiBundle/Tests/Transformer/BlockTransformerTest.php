<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Phake;
use OpenOrchestra\ApiBundle\Transformer\BlockTransformer;

/**
 * Class BlockTransformerTest
 */
class BlockTransformerTest extends AbstractBaseTestCase
{
    protected $facadeClass = 'OpenOrchestra\ApiBundle\Facade\BlockFacade';
    protected $displayBlockManager;
    protected $transformerManager;
    protected $blockTransformer;
    protected $nodeRepository;
    protected $router;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->displayBlockManager = Phake::mock('OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockManager');
        $this->displayIconManager = Phake::mock('OpenOrchestra\Backoffice\DisplayIcon\DisplayManager');
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');

        $this->router = Phake::mock('Symfony\Component\Routing\RouterInterface');
        Phake::when($this->router)->generate(Phake::anyParameters())->thenReturn('route');
        $this->transformerManager = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        Phake::when($this->transformerManager)->getRouter()->thenReturn($this->router);

        $this->blockTransformer = new BlockTransformer(
            $this->facadeClass,
            $this->displayBlockManager,
            $this->displayIconManager,
            $this->nodeRepository,
            $this->translator
        );
        $this->blockTransformer->setContext($this->transformerManager);
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $name = $this->blockTransformer->getName();

        $this->assertSame('block', $name);
    }

    /**
     * Test transform
     *
     * @param string     $component
     * @param array      $attributes
     * @param string     $label
     * @param array|null $expectedAttributes
     *
     * @dataProvider blockTransformProvider
     */
    public function testTransform(
        $component,
        $attributes,
        $label,
        $expectedAttributes = null
    )
    {
        $html = 'ok';
        $block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        $response = Phake::mock('Symfony\Component\HttpFoundation\Response');
        $transformer = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerInterface');
        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\UiModelFacade');

        Phake::when($block)->getComponent()->thenReturn($component);
        Phake::when($block)->getLabel()->thenReturn($label);
        Phake::when($block)->getAttributes()->thenReturn($attributes);
        Phake::when($this->displayBlockManager)->show($block)->thenReturn($response);
        Phake::when($response)->getContent()->thenReturn($html);
        Phake::when($this->displayIconManager)->show($component)->thenReturn('icon');

        Phake::when($this->transformerManager)->get('ui_model')->thenReturn($transformer);
        Phake::when($transformer)->transform(Phake::anyParameters())->thenReturn($facade);

        $facadeResult = $this->blockTransformer->transform($block, true);

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\BlockFacade', $facadeResult);
        $this->assertSame($component, $facadeResult->component);
        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\UiModelFacade', $facadeResult->uiModel);
        $this->assertArrayHasKey('_self_form', $facadeResult->getLinks());
        if (is_null($expectedAttributes)) {
            $expectedAttributes = $attributes;
        }
        $this->assertSame($expectedAttributes, $facadeResult->getAttributes());
        Phake::verify($this->router)->generate(Phake::anyParameters());

        if (!$label) {
            Phake::verify($this->translator)->trans('open_orchestra_backoffice.block.' . $component . '.title');
        }
    }

    /**
     * @return array
     */
    public function blockTransformProvider()
    {
        return array(
            array('sample', array('title' => 'title one', 'author' => 'me'), 'Sample'),
            array('sample', array('title' => 'news', 'author' => 'benj', 'text' => 'Hello world'), 'Sample'),
            array('news', array('title' => 'news', 'author' => 'benj', 'text' => 'Hello everybody'), 'News'),
            array('menu', array(), 'Menu'),
            array('menu', array('array' => array('test' => 'test')), 'Menu', array('array' => '{"test":"test"}')),
            array('menu', array(), null),
        );
    }

    /**
     * @param array $areas
     * @param string $nodeId
     * @param string $nodeMongoId
     * @param bool   $isInside
     * @param bool   $isDeletable
     *
     * @dataProvider provideBlockDeletable
     */
    public function testBlockTransformerIsDeletable(array $areas, $isInside, $isDeletable)
    {
        $html = 'ok';
        $component = 'fakeComponent';
        $label = 'fakeLabel';
        $block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        $response = Phake::mock('Symfony\Component\HttpFoundation\Response');
        $transformer = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerInterface');
        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\UiModelFacade');

        Phake::when($block)->getComponent()->thenReturn($component);
        Phake::when($block)->getLabel()->thenReturn($label);
        Phake::when($block)->getAttributes()->thenReturn(array());
        Phake::when($this->displayBlockManager)->show($block)->thenReturn($response);
        Phake::when($response)->getContent()->thenReturn($html);
        Phake::when($this->displayIconManager)->show($component)->thenReturn('icon');

        Phake::when($this->transformerManager)->get('ui_model')->thenReturn($transformer);
        Phake::when($transformer)->transform(Phake::anyParameters())->thenReturn($facade);

        $facadeResult = $this->blockTransformer->transform($block, $isInside);

        $this->assertSame($facadeResult->isDeletable, $isDeletable);
    }

    /**
     * @return array
     */
    public function provideBlockDeletable()
    {
        return array(
            array(array(),  false, true),
        );
    }


    /**
     * Test Exception transform with wrong object a parameters
     */
    public function testExceptionTransform()
    {
        $this->expectException('OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException');
        $this->blockTransformer->transform(Phake::mock('stdClass'));
    }
}
