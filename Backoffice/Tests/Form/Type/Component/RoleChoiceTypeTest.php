<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type\Component;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\Type\Component\RoleChoiceType;

/**
 * Class RoleChoiceTypeTest
 */
class RoleChoiceTypeTest extends AbstractBaseTestCase
{
    /**
     * @var RoleChoiceType
     */
    protected $form;

    protected $roleCollector;
    protected $roleCollector2;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->roleCollector = Phake::mock('OpenOrchestra\Backoffice\Collector\RoleCollectorInterface');
        $this->roleCollector2 = Phake::mock('OpenOrchestra\Backoffice\Collector\RoleCollectorInterface');

        $this->form = new RoleChoiceType(array($this->roleCollector, $this->roleCollector2), 'oo_role_choice', array ('role_test' => array('category' => 'role_test_category', 'label' => 'role_test_label', 'order' => 0)));
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
        $this->assertSame('oo_role_choice', $this->form->getName());
    }

    /**
     * Test parent
     */
    public function testParent()
    {
        $this->assertSame('choice', $this->form->getParent());
    }

    /**
     * Test builder
     */
    public function testBuilder()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->addEventSubscriber(Phake::anyParameters())->thenReturn($builder);

        $this->form->buildForm($builder, array());

        Phake::verify($builder, Phake::never())->add(Phake::anyParameters());
        Phake::verify($builder, Phake::never())->addEventSubscriber(Phake::anyParameters());
        Phake::verify($builder, Phake::never())->addEventListener(Phake::anyParameters());
    }

    /**
     * @param array  $roles
     * @param array  $rolesCollector1
     * @param array  $rolesCollector2
     *
     * @dataProvider provideRoleAndTranslation
     */
    public function testConfigureOptions(array $roles, $rolesCollector1, $rolesCollector2)
    {
        Phake::when($this->roleCollector)->getRoles()->thenReturn($rolesCollector1);
        Phake::when($this->roleCollector2)->getRoles()->thenReturn($rolesCollector2);

        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'choices' => $roles
        ));

    }

    /**
     * @return array
     */
    public function provideRoleAndTranslation()
    {
        return array(
            array(array('foo' => 'bar'), array('foo' => 'bar'), array()),
            array(array('foo' => 'bar', 'bar' => 'bar'), array('foo' => 'bar', 'bar' => 'bar'), array()),
            array(array('FOO' => 'bar', 'BAR' => 'bar'), array('FOO' => 'bar', 'BAR' => 'bar'), array()),
            array(array('FOO' => 'bar', 'BAR' => 'bar'), array('FOO' => 'bar'), array('BAR' => 'bar')),
        );
    }

    /**
     * test buildView
     */
    public function testBuildView()
    {
        Phake::when($this->roleCollector)->getRoles()->thenReturn(array('ROLE_TEST' => 'label'));
        Phake::when($this->roleCollector2)->getRoles()->thenReturn(array());

        $formInterface = Phake::mock('Symfony\Component\Form\FormInterface');
        $formView = Phake::mock('Symfony\Component\Form\FormView');

        $this->form->buildView($formView, $formInterface, array());
        $this->assertEquals($formView->vars['rolesOrdered'], array (
            'role_test_category' => array (
                'role_test_label' => array (
                    0 => 0,
                ),
            ),
        ));
    }
}
