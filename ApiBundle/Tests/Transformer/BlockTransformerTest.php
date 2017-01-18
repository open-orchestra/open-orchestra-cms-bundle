<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\ApiBundle\Transformer\BlockTransformer;

/**
 * Class BlockTransformerTest
 */
class BlockTransformerTest extends AbstractBaseTestCase
{
    protected $facadeClass = 'OpenOrchestra\ApiBundle\Facade\BlockFacade';
    protected $displayBlockManager;
    protected $blockTransformer;
    protected $blockConfigurationManager;
    protected $translator;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->displayBlockManager = Phake::mock('OpenOrchestra\Backoffice\DisplayBlock\DisplayBlockManager');
        $this->blockConfigurationManager = Phake::mock('OpenOrchestra\Backoffice\Manager\BlockConfigurationManager');
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        $nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $context = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        $groupContext = Phake::mock('OpenOrchestra\BaseApi\Context\GroupContext');
        Phake::when($groupContext)->hasGroup(Phake::anyParameters())->thenReturn(false);
        Phake::when($context)->getGroupContext()->thenReturn($groupContext);

        $this->blockTransformer = new BlockTransformer(
            $this->facadeClass,
            $this->displayBlockManager,
            $this->blockConfigurationManager,
            $this->translator,
            $nodeRepository
        );
        $this->blockTransformer->setContext($context);
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

        Phake::when($block)->getComponent()->thenReturn($component);
        Phake::when($block)->getLabel()->thenReturn($label);
        Phake::when($block)->getAttributes()->thenReturn($attributes);
        Phake::when($this->displayBlockManager)->show($block)->thenReturn($html);

        $facadeResult = $this->blockTransformer->transform($block);

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\BlockFacade', $facadeResult);
        $this->assertSame($component, $facadeResult->component);
        if (is_null($expectedAttributes)) {
            $expectedAttributes = $attributes;
        }
        $this->assertSame($expectedAttributes, $facadeResult->getAttributes());
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
     * Test Exception transform with wrong object a parameters
     */
    public function testExceptionTransform()
    {
        $this->expectException('OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException');
        $this->blockTransformer->transform(Phake::mock('stdClass'));
    }
}
