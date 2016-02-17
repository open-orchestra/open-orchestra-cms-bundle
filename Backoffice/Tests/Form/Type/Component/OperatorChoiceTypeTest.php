<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type\Component;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\Type\Component\OperatorChoiceType;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class OperatorChoiceTypeTest
 */
class OperatorChoiceTypeTest extends AbstractBaseTestCase
{
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new OperatorChoiceType();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->form);
    }

    /**
     * Test parent
     */
    public function testGetParent()
    {
        $this->assertEquals('choice', $this->form->getParent());
    }

    /**
     * Test Name
     */
    public function testGetName()
    {
        $this->assertEquals('oo_operator_choice', $this->form->getName());
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(
            array(
                'empty_data' => ContentRepositoryInterface::CHOICE_AND,
                'constraints' => new NotBlank(),
                'choices' => array(
                    ContentRepositoryInterface::CHOICE_AND => 'open_orchestra_backoffice.form.content_list.choice_type_and',
                    ContentRepositoryInterface::CHOICE_OR => 'open_orchestra_backoffice.form.content_list.choice_type_or',
                ),
            )
        );
    }
}
