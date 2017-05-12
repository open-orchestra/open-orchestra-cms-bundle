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
    protected $templateManager;
    protected $contextManager;
    protected $generateFormManager;
    protected $blockToArrayTransformer;
    protected $blockFormTypeSubscriber;
    protected $templateName = 'template';
    protected $siteRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        $this->templateManager = Phake::mock('OpenOrchestra\Backoffice\Manager\TemplateManager');
        $this->contextManager = Phake::mock('OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface');
        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        $this->generateFormManager = Phake::mock('OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager');
        $this->blockToArrayTransformer = Phake::mock('OpenOrchestra\Backoffice\Form\DataTransformer\BlockToArrayTransformer');
        $this->blockFormTypeSubscriber = Phake::mock('OpenOrchestra\Backoffice\EventSubscriber\BlockFormTypeSubscriber');

        Phake::when($this->templateManager)->getTemplateSetParameters()->thenReturn(array('fakeTemplateSet' => array('styles' => array())));
        Phake::when($site)->getTemplateSet()->thenReturn('fakeTemplateSet');
        Phake::when($this->siteRepository)->findOneBySiteId(Phake::anyParameters())->thenReturn($site);
        Phake::when($this->contextManager)->getCurrentSiteId()->thenReturn('fakeSiteId');
        Phake::when($this->generateFormManager)->getTemplate(Phake::anyParameters())->thenReturn($this->templateName);
        Phake::when($this->generateFormManager)->getTemplate(Phake::anyParameters())->thenReturn($this->templateName);

        $this->blockType = new BlockType(
            $this->templateManager,
            $this->contextManager,
            $this->siteRepository,
            $this->generateFormManager,
            $this->blockToArrayTransformer,
            $this->blockFormTypeSubscriber
        );
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
     * Test build view
     */
    public function testBuildView()
    {
        $view = Phake::mock('Symfony\Component\Form\FormView');
        $form = Phake::mock('Symfony\Component\Form\Form');
        $options = array(
            'delete_button' => true,
            'new_button' => true,
        );
        $this->blockType->buildView($view, $form, $options);
        $this->assertTrue($view->vars['delete_button']);
        $this->assertTrue($view->vars['new_button']);
    }

    /**
     * Test configureOptions
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->blockType->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'data_class' => null,
            'group_enabled' => true,
            'delete_button' => false,
            'new_button' => false,
            'group_render' => array(
                'property' => array(
                    'rank' => 0,
                    'label' => 'open_orchestra_backoffice.form.block.group.property',
                ),
                'data' => array(
                    'rank' => 1,
                    'label' => 'open_orchestra_backoffice.form.block.group.data',
                ),
                'technical' => array(
                    'rank' => 2,
                    'label' => 'open_orchestra_backoffice.form.block.group.technical',
                ),
            ),
            'sub_group_render' => array(
                'property' => array(
                    'rank' => 0,
                    'label' => 'open_orchestra_backoffice.form.block.sub_group.property',
                ),
                'style' => array(
                    'rank' => 1,
                    'label' => 'open_orchestra_backoffice.form.block.sub_group.style',
                ),
                'content' => array(
                    'rank' => 0,
                    'label' => 'open_orchestra_backoffice.form.block.sub_group.content',
                ),
                'cache' => array(
                    'rank' => 0,
                    'label' => 'open_orchestra_backoffice.form.block.sub_group.cache',
                ),
                'code' => array(
                    'rank' => 1,
                    'label' => 'open_orchestra_backoffice.form.block.sub_group.code',
                ),
                'html' => array(
                    'rank' => 1,
                    'label' => 'open_orchestra_backoffice.form.block.sub_group.html',
                ),
            ),
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
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);

        $this->blockType->buildForm($builder, $options);

        Phake::verify($builder, Phake::times(2))->add(Phake::anyParameters());
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
