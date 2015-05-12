<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use OpenOrchestra\BackofficeBundle\Form\Type\OrchestraChoicesOption;
use Phake;

/**
 * Class OrchestraChoicesOptionTest
 */
class OrchestraChoicesOptionTest extends \PHPUnit_Framework_TestCase
{
    protected $form;
    protected $transformer;
    protected $builder;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        $this->transformer = Phake::mock('OpenOrchestra\BackofficeBundle\Form\DataTransformer\ChoicesOptionToArrayTransformer');
        $this->form = new OrchestraChoicesOption($this->transformer);
    }

    /**
     * Test model transformer
     */
    public function testBuildForm()
    {
        $this->form->buildForm($this->builder, array('embedded' => true));

        Phake::verify($this->builder)->addModelTransformer($this->transformer);
    }

    /**
     * Test parent
     */
    public function testGetParent()
    {
        $this->assertEquals('text', $this->form->getParent());
    }

    /**
     * test Name
     */
    public function testGetName()
    {
        $this->assertEquals('orchestra_choices_option', $this->form->getName());
    }
}