<?php

namespace OpenOrchestra\UserAdminBundle\Tests\Form\Type;

use OpenOrchestra\UserAdminBundle\Form\Type\RegistrationUserType;
use Phake;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class RegistrationUserTypeTest
 */
class RegistrationUserTypeTest extends AbstractUserTypeTest
{
    /**
     * @var RegistrationUserType
     */
    protected $form;
    protected $class = 'OpenOrchestra\UserBundle\Document\User';

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();

        $this->form = new RegistrationUserType();
    }

    /**
     * Test get parent
     */
    public function testGetParent()
    {
        $this->assertEquals($this->form->getParent(), 'oo_user');
    }

    /**
     * Test builder
     */
    public function testBuilder()
    {
        $this->form->buildForm($this->builder, array());

        Phake::verify($this->builder)->add('username', 'text', array(
            'label' => 'form.username',
            'translation_domain' => 'FOSUserBundle',
            'group_id' => 'information',
            'sub_group_id' => 'contact_information',
        ));
    }
}
