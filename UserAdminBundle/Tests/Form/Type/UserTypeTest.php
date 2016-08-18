<?php

namespace OpenOrchestra\UserAdminBundle\Tests\Form\Type;

use OpenOrchestra\UserAdminBundle\Form\Type\UserType;
use Phake;

/**
 * Class UserTypeTest
 */
class UserTypeTest extends AbstractUserTypeTest
{
    /**
     * @var UserType
     */
    protected $form;

    protected $class = 'OpenOrchestra\UserBundle\Document\User';
    protected $twig;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $this->twig = Phake::mock('Twig_Environment');
        $parameters = array(0 => 'en', 1 => 'fr');

        $this->form = new UserType($this->class, $parameters);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('oo_user', $this->form->getName());
    }

    /**
     * Test builder
     *
     * @param array   $options
     * @param boolean $expectSubscriber
     *
     * @dataProvider provideOptions
     */
    public function testBuilder(array $options, $expectSubscriber)
    {
        $this->form->buildForm($this->builder, $options);

        Phake::verify($this->builder, Phake::times(5))->add(Phake::anyParameters());

        if ($expectSubscriber) {
            Phake::verify($this->builder)->addEventSubscriber(Phake::anyParameters());
        }
    }

    /**
     * Provide form type options
     *
     * @return array
     */
    public function provideOptions()
    {
        return array(
            'without_groups_edition' => array(array(), false),
            'with_groups_edition' => array(array('edit_groups' => 'true'), true)
        );
    }

    /**
     * Test configureOptions
     */
    public function testResolver()
    {
        $this->form->configureOptions($this->resolver);
        Phake::verify($this->resolver)->setDefaults(array(
            'data_class' => $this->class,
            'edit_groups' => true
        ));
        Phake::verify($this->resolver)->setDefaults(Phake::anyParameters());
    }
}
