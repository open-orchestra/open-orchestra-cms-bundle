<?php

namespace OpenOrchestra\GroupBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\UserAdminBundle\Form\Type\MemberElementType;

/**
 * Class MemberElementTypeTest
 */
class MemberElementTypeTest extends AbstractBaseTestCase
{
    /**
     * @var MemberElementType
     */
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $user = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($user)->getId()->thenReturn('fakeId');
        Phake::when($user)->getFirstName()->thenReturn('fakeFirstName');
        Phake::when($user)->getLastName()->thenReturn('fakeLastName');
        Phake::when($user)->getEmail()->thenReturn('fakeEmail');

        $userRepository = Phake::mock('OpenOrchestra\UserBundle\Repository\UserRepository');
        Phake::when($userRepository)->find(Phake::anyParameters())->thenReturn($user);

        $this->form = new MemberElementType($userRepository);
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
        $this->assertSame('oo_member_element', $this->form->getName());
    }

    /**
     * Test builder
     */
    public function testBuilder()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);

        $this->form->buildForm($builder, array('property_path' => '[fakePropertyPath]'));

        Phake::verify($builder, Phake::times(1))->add('member', 'radio', array(
            'label' => false,
            'value' => 'fakePropertyPath',
            'data' => true,
        ));
    }

    /**
     * test buildView
     */
    public function testBuildView()
    {

        $formInterface = Phake::mock('Symfony\Component\Form\FormInterface');
        $formView = Phake::mock('Symfony\Component\Form\FormView');
        $formView->vars['name'] = 'fakeName';

        $this->form->buildView($formView, $formInterface, array());
        $this->assertEquals(array(
                'id' => 'fakeId',
                'firstName' => 'fakeFirstName',
                'lastName' => 'fakeLastName',
                'email' => 'fakeEmail',
            ), $formView->vars['parameters']);
    }
}
