<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type;

use OpenOrchestra\Backoffice\Form\Type\BlockType;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Test BlockTypeTest
 */
class BlockTypeTest extends AbstractBaseTestCase
{
    /**
     * @var BlockType
     */
    protected $blockType;

    protected $templateName = 'template';
    protected $generateFormManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->generateFormManager = Phake::mock('OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager');
        $blockToArrayTransformer = Phake::mock('OpenOrchestra\Backoffice\Form\DataTransformer\BlockToArrayTransformer');
        $blockFormTypeSubscriber = Phake::mock('OpenOrchestra\Backoffice\EventSubscriber\BlockFormTypeSubscriber');

        Phake::when($this->generateFormManager)->getTemplate(Phake::anyParameters())->thenReturn($this->templateName);

        $this->blockType = new BlockType($this->generateFormManager, $blockToArrayTransformer, $blockFormTypeSubscriber);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->blockType);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('oo_block', $this->blockType->getName());
    }

    /**
     * Test configureOptions
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->blockType->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'blockPosition' => 0,
            'data_class' => null
        ));
    }

    /**
     * @param array $options
     * @param int   $subscriberCount
     *
     * @dataProvider provideOptionsAndCount
     */
    public function testBuildForm(array $options, $subscriberCount)
    {
        $block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        $options = array_merge(array('blockPosition' => 0, 'data' => $block), $options);
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');

        $this->blockType->buildForm($builder, $options);

        Phake::verify($builder, Phake::times(4))->add(Phake::anyParameters());
        Phake::verify($builder)->setAttribute('template', $this->templateName);
        Phake::verify($builder)->addViewTransformer(Phake::anyParameters());
        Phake::verify($builder, Phake::times($subscriberCount))->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideOptionsAndCount()
    {
        return array(
            array(array(), 1),
            array(array('disabled' => false), 1),
            array(array('disabled' => true), 1),
        );
    }
}
