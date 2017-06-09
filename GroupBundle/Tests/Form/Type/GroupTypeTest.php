<?php

namespace OpenOrchestra\GroupBundle\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\GroupBundle\Form\Type\GroupType;

/**
 * Class GroupTypeTest
 */
class GroupTypeTest extends AbstractBaseTestCase
{
    /**
     * @var GroupType
     */
    protected $form;
    protected $eventDispatcher;

    protected $groupClass = 'groupClass';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $eventSubscriber1 = Phake::mock('Symfony\Component\EventDispatcher\EventSubscriberInterface');
        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dataTransformer0 = Phake::mock('Symfony\Component\Form\DataTransformerInterface');
        $dataTransformer1 = Phake::mock('Symfony\Component\Form\DataTransformerInterface');
        $generatePerimeterManager = Phake::mock('OpenOrchestra\Backoffice\GeneratePerimeter\GeneratePerimeterManager');
        Phake::when($generatePerimeterManager)->getPerimetersConfiguration(Phake::anyParameters())->thenReturn(array());
        $this->form = new GroupType(
            $eventSubscriber1,
            $this->eventDispatcher,
            $dataTransformer0,
            $dataTransformer1,
            $generatePerimeterManager,
            $this->groupClass,
            array('en', 'fr')
        );
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->form);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('oo_group', $this->form->getName());
    }

    /**
     * Test builder
     *
     * @param bool $creation
     * @param int  $formTimes
     * @param int  $transformerTimes
     * @param int  $subscriberTimes
     * @param int  $dispatcherTimes
     *
     * @dataProvider provideBuilderParams
     */
    public function testBuilder($creation, $formTimes, $transformerTimes, $subscriberTimes, $dispatcherTimes)
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->get(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->addEventSubscriber(Phake::anyParameters())->thenReturn($builder);

        $site = Phake::mock('OpenOrchestra\ModelBundle\Document\Site');
        Phake::when($site)->getSiteId()->thenReturn('siteId');
        $group = Phake::mock('OpenOrchestra\GroupBundle\Document\Group');
        Phake::when($group)->getSite()->thenReturn($site);

        $this->form->buildForm(
            $builder,
            array(
                'new_button' => false,
                'creation'   => $creation,
                'data'       => $group
            )
        );

        Phake::verify($builder, Phake::times($formTimes))->add(Phake::anyParameters());
        Phake::verify($builder, Phake::times($transformerTimes))->addModelTransformer(Phake::anyParameters());
        Phake::verify($builder, Phake::times($subscriberTimes))->addEventSubscriber(Phake::anyParameters());
        Phake::verify($this->eventDispatcher, Phake::times($dispatcherTimes))->dispatch(Phake::anyParameters());
    }

    /**
     * Provide builder params
     *
     * @return array
     */
    public function provideBuilderParams()
    {
        return array(
            array(true , 2, 0, 0, 0),
            array(false, 4, 2, 1, 1),
        );
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(
            array(
                'data_class' => $this->groupClass,
                'delete_button' => false,
                'new_button' => false,
                'enable_delete_button' => false,
                'delete_help_text' => 'open_orchestra_group.form.group.delete_help_text',
                'creation' => false,
                'group_enabled' => true,
                'group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_group.form.group.group.property',
                    ),
                    'right' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_group.form.group.group.right',
                    ),
                    'perimeter' => array(
                        'rank' => 3,
                        'label' => 'open_orchestra_group.form.group.group.perimeter',
                    )
                ),
                'sub_group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_group.form.group.sub_group.property',
                    ),
                    'page' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_group.form.group.sub_group.page',
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
            'new_button'    => false,
            'creation'      => true,
            'enable_delete_button' => true,
            'delete_help_text' => 'test',
        );
        $this->form->buildView($view, $form, $options);
        $this->assertTrue($view->vars['delete_button']);
        $this->assertTrue($view->vars['enable_delete_button']);
        $this->assertFalse($view->vars['new_button']);
        $this->assertSame('test', $view->vars['delete_help_text']);
    }
}
