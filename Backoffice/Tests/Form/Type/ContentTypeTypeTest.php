<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\Type\ContentTypeType;

/**
 * Class ContentTypeTypeTest
 */
class ContentTypeTypeTest extends AbstractBaseTestCase
{
    protected $form;
    protected $translator;
    protected $class = 'content_type';
    protected $translatedLabel = 'existing option';
    protected $contentTypeTypeSubscriber;
    protected $contentTypeStatusableSubscriber;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn($this->translatedLabel);
        $this->contentTypeTypeSubscriber = Phake::mock('OpenOrchestra\Backoffice\EventSubscriber\ContentTypeTypeSubscriber');
        $this->contentTypeStatusableSubscriber = Phake::mock('OpenOrchestra\Backoffice\EventSubscriber\ContentTypeStatusableSubscriber');

        $this->form = new ContentTypeType(
            $this->class,
            $this->translator,
            array(),
            $this->contentTypeTypeSubscriber,
            $this->contentTypeStatusableSubscriber
        );
    }

    /**
     * Test Name
     */
    public function testName()
    {
        $this->assertSame('oo_content_type', $this->form->getName());
    }

    /**
     * Test builder
     */
    public function testBuilder()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->addEventSubscriber(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->addModelTransformer(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->get(Phake::anyParameters())->thenReturn($builder);

        $this->form->buildForm($builder, array());

        Phake::verify($builder, Phake::times(10))->add(Phake::anyParameters());
        Phake::verify($builder)->addEventSubscriber($this->contentTypeTypeSubscriber);
        Phake::verify($builder)->addEventSubscriber($this->contentTypeStatusableSubscriber);

        Phake::verify($this->translator)->trans('open_orchestra_backoffice.form.field_type.add');
        Phake::verify($this->translator)->trans('open_orchestra_backoffice.form.field_type.new');
        Phake::verify($this->translator)->trans('open_orchestra_backoffice.form.field_type.delete');
    }

    /**
     * Test the default options
     */
    public function testConfigureOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(
            array(
                'data_class' => $this->class,
                'delete_button' => false,
                'new_button' => false,
                'group_enabled' => true,
                'group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.content_type.group.property',
                    ),
                    'field' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_backoffice.form.content_type.group.field',
                    ),
                ),
                'sub_group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.content_type.sub_group.property',
                    ),
                    'customization' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_backoffice.form.content_type.sub_group.customization',
                    ),
                    'share' => array(
                        'rank' => 2,
                        'label' => 'open_orchestra_backoffice.form.content_type.sub_group.share',
                    ),
                    'visible' => array(
                        'rank' => 3,
                        'label' => 'open_orchestra_backoffice.form.content_type.sub_group.visible',
                    ),
                    'version' => array(
                        'rank' => 4,
                        'label' => 'open_orchestra_backoffice.form.content_type.sub_group.version',
                    ),
                ),
            )
        );
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
        $this->form->buildView($view, $form, $options);
        $this->assertTrue($view->vars['delete_button']);
        $this->assertTrue($view->vars['new_button']);
    }
}
