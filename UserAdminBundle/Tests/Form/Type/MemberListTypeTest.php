<?php

namespace OpenOrchestra\GroupBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\UserAdminBundle\Form\Type\MemberListType;

/**
 * Class MemberListTypeTest
 */
class MemberListTypeTest extends AbstractBaseTestCase
{
    /**
     * @var MemberListType
     */
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new MemberListType();
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
        $this->assertSame('oo_member_list', $this->form->getName());
    }

    /**
     * Test builder
     */
    public function testBuilder()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);

        $this->form->buildForm($builder, array('data' => array()));

        Phake::verify($builder, Phake::times(1))->add('members_collection', 'collection', array(
            'type' => 'oo_member_element',
            'label' => 'open_orchestra_group.form.group.member',
            'data' => array(),
        ));
    }
}
