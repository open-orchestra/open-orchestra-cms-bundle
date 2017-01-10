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
        $eventSubscriber0 = Phake::mock('Symfony\Component\EventDispatcher\EventSubscriberInterface');
        $eventSubscriber1 = Phake::mock('Symfony\Component\EventDispatcher\EventSubscriberInterface');
        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dataTransformer0 = Phake::mock('Symfony\Component\Form\DataTransformerInterface');
        $dataTransformer1 = Phake::mock('Symfony\Component\Form\DataTransformerInterface');
        $generatePerimeterManager = Phake::mock('OpenOrchestra\Backoffice\GeneratePerimeter\GeneratePerimeterManager');
        Phake::when($generatePerimeterManager)->getPerimetersConfiguration()->thenReturn(array());
        $this->form = new GroupType(
            $eventSubscriber0,
            $eventSubscriber1,
            $this->eventDispatcher,
            $dataTransformer0,
            $dataTransformer1,
            $generatePerimeterManager,
            $this->groupClass,
            array('en', 'fr'));
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
     */
    public function testBuilder()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->get(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->addEventSubscriber(Phake::anyParameters())->thenReturn($builder);

        $this->form->buildForm($builder, array());

        Phake::verify($builder, Phake::times(5))->add(Phake::anyParameters());
        Phake::verify($builder, Phake::times(2))->addModelTransformer(Phake::anyParameters());
        Phake::verify($builder, Phake::times(2))->addEventSubscriber(Phake::anyParameters());
        Phake::verify($this->eventDispatcher, Phake::times(1))->dispatch(Phake::anyParameters());
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
                'data_class' => $this->groupClass,
                'delete_button' => false,
                'new_button' => false,
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
                    ),
                    'member' => array(
                        'rank' => 4,
                        'label' => 'open_orchestra_group.form.group.group.member',
                    ),
                ),
                'sub_group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_group.form.group.sub_group.property',
                    ),
                    'right' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_group.form.group.sub_group.right',
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
            'new_button' => true,
        );
        $this->form->buildView($view, $form, $options);
        $this->assertTrue($view->vars['delete_button']);
        $this->assertTrue($view->vars['new_button']);
    }
}
