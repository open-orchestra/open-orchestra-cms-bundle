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

    protected $roles;
    protected $role1;
    protected $role2;
    protected $descriptions;
    protected $roleRepository;
    protected $role1Name = 'role1Name';
    protected $role2Name = 'role2Name';
    protected $translationChoiceManager;
    protected $description = 'description';

    public function setUp()
    {
        $this->translationChoiceManager = Phake::mock('OpenOrchestra\Backoffice\Manager\TranslationChoiceManager');
        Phake::when($this->translationChoiceManager)->choose(Phake::anyParameters())->thenReturn($this->description);

        $description = Phake::mock('OpenOrchestra\ModelInterface\Model\TranslatedValueInterface');
        $this->descriptions = new ArrayCollection(array($description));

        $this->role1 = Phake::mock('OpenOrchestra\ModelInterface\Model\RoleInterface');
        Phake::when($this->role1)->getName()->thenReturn($this->role1Name);
        Phake::when($this->role1)->getDescriptions()->thenReturn($this->descriptions);
        $this->role2 = Phake::mock('OpenOrchestra\ModelInterface\Model\RoleInterface');
        Phake::when($this->role2)->getName()->thenReturn($this->role2Name);
        Phake::when($this->role2)->getDescriptions()->thenReturn($this->descriptions);

        $this->roles = new ArrayCollection();
        $this->roles->add($this->role1);
        $this->roles->add($this->role2);

        $this->roleRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface');
        Phake::when($this->roleRepository)->findAccessRole()->thenReturn($this->roles);

        $this->form = new OrchestraRoleChoiceType($this->roleRepository, $this->translationChoiceManager);
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
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'choices' => array(
                $this->role1Name => $this->description,
                $this->role2Name => $this->description,
            )
        ));
    }
}
