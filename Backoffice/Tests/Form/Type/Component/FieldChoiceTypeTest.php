<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type\Component;

use OpenOrchestra\Backoffice\Form\Type\Component\FieldChoiceType;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class FieldChoiceTypeTest
 */
class FieldChoiceTypeTest extends AbstractBaseTestCase
{
    protected $form;
    protected $builder;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        $this->form = new FieldChoiceType();
    }

    /**
     * @param boolean $multiple
     * @dataProvider providerOptionMultiple
     */
    public function testBuildForm($multiple)
    {
        $this->form->buildForm($this->builder, array('multiple' => $multiple));

        if (false === $multiple) {
            Phake::verify($this->builder)->addEventListener(Phake::anyParameters());
        }
    }

    /**
     * @return array
     */
    public function providerOptionMultiple()
    {
        return array(
            array(true),
            array(false),
        );
    }

    /**
     * Test parent
     */
    public function testGetParent()
    {
        $this->assertEquals('choice', $this->form->getParent());
    }

    /**
     * test Name
     */
    public function testGetName()
    {
        $this->assertEquals('oo_field_choice', $this->form->getName());
    }
}
