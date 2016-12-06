<?php

namespace OpenOrchestra\GroupBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\GroupBundle\Form\Type\GroupListType;

/**
 * Class GroupListTypeTest
 */
class GroupListTypeTest extends Phake
{
    /**
     * @var GroupListType
     */
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $transformer = Phake::mock('OpenOrchestra\GroupBundle\Form\DataTransformer\GroupListToArrayTransformer');
        $this->form = new GroupListType($transformer);
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
        $this->assertSame('oo_group_list', $this->form->getName());
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

        Phake::verify($builder, Phake::times(1))->add(Phake::anyParameters());
        Phake::verify($builder, Phake::times(1))->addModelTransformer(Phake::anyParameters());
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'allowed_sites' => null,
        ));
    }
}
