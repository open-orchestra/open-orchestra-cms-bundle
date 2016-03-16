<?php

namespace OpenOrchestra\UserBundle\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\Type\RoleType;
use Symfony\Component\Form\FormEvents;

/**
 * Class RoleTypeTest
 */
class RoleTypeTest extends AbstractBaseTestCase
{
    /**
     * @var RoleType
     */
    protected $form;

    protected $roleClass = 'roleClass';
    protected $roleTypeSubscriber;
    protected $translateValueInitializer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translateValueInitializer = Phake::mock('OpenOrchestra\Backoffice\EventListener\TranslateValueInitializerListener');
        $this->roleTypeSubscriber = Phake::mock('OpenOrchestra\Backoffice\EventSubscriber\RoleTypeSubscriber');

        $this->form = new RoleType($this->translateValueInitializer, $this->roleTypeSubscriber, $this->roleClass);
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
        $this->assertSame('oo_role', $this->form->getName());
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

        Phake::verify($builder, Phake::times(4))->add(Phake::anyParameters());
        Phake::verify($builder)->addEventListener(
            FormEvents::PRE_SET_DATA,
            array($this->translateValueInitializer, 'preSetData')
        );
        Phake::verify($builder)->addEventSubscriber($this->roleTypeSubscriber);
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'data_class' => $this->roleClass
        ));
    }
}
