<?php

namespace PHPOrchestra\UserBundle\Test\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use PHPOrchestra\UserBundle\Form\Type\OrchestraRoleType;

/**
 * Class OrchestraRoleTypeTest
 */
class OrchestraRoleTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrchestraRoleType
     */
    protected $form;

    protected $roleRepository;
    protected $roles;
    protected $role1;
    protected $role1Name = 'role1Name';
    protected $role2;
    protected $role2Name = 'role2Name';

    public function setUp()
    {
        $this->role1 = Phake::mock('PHPOrchestra\ModelBundle\Document\Role');
        Phake::when($this->role1)->getName()->thenReturn($this->role1Name);
        $this->role2 = Phake::mock('PHPOrchestra\ModelBundle\Document\Role');
        Phake::when($this->role2)->getName()->thenReturn($this->role2Name);

        $this->roles = new ArrayCollection();
        $this->roles->add($this->role1);
        $this->roles->add($this->role2);

        $this->roleRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\RoleRepository');
        Phake::when($this->roleRepository)->findAll()->thenReturn($this->roles);

        $this->form = new OrchestraRoleType($this->roleRepository);
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
     * Test resolver
     */
    public function testSetDefaultOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->form->setDefaultOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'choices' => array(
                $this->role1Name => $this->role1Name,
                $this->role2Name => $this->role2Name,
            )
        ));
    }
}
