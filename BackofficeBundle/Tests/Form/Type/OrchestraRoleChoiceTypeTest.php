<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use Phake;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BackofficeBundle\Form\Type\OrchestraRoleChoiceType;

/**
 * Class OrchestraRoleChoiceTypeTest
 */
class OrchestraRoleChoiceTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrchestraRoleChoiceType
     */
    protected $form;

    protected $translator;
    protected $roleCollector;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');

        $this->roleCollector = Phake::mock('OpenOrchestra\Backoffice\Collector\RoleCollector');

        $this->form = new OrchestraRoleChoiceType($this->roleCollector, $this->translator);
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
        $this->assertSame('orchestra_role_choice', $this->form->getName());
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
     * @param string $translation
     * @param array  $expectedChoices
     *
     * @dataProvider provideRoleAndTranslation
     */
    public function testConfigureOptions(array $roles, $translation, array $expectedChoices)
    {
        Phake::when($this->roleCollector)->getRoles()->thenReturn($roles);
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn($translation);

        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'choices' => $expectedChoices
        ));
        foreach ($roles as $role) {
            Phake::verify($this->translator)->trans('open_orchestra_role.' . strtolower($role));
        }

    }

    /**
     * @return array
     */
    public function provideRoleAndTranslation()
    {
        return array(
            array(array('foo'), 'bar', array('foo' => 'bar')),
            array(array('foo', 'bar'), 'bar', array('foo' => 'bar', 'bar' => 'bar')),
            array(array('FOO', 'BAR'), 'bar', array('FOO' => 'bar', 'BAR' => 'bar')),
        );
    }
}
