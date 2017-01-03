<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type\Component;

use OpenOrchestra\Backoffice\Form\Type\Component\CheckListType;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class CheckListTypeTest
 */
class CheckListTypeTest extends AbstractBaseTestCase
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
        $this->transformer = Phake::mock('Symfony\Component\Form\DataTransformerInterface');
        $this->form = new CheckListType($this->transformer);
    }

    /**
     * Test model transformer
     */
    public function testBuildForm()
    {
        $this->form->buildForm($this->builder, array());

        Phake::verify($this->builder)->addModelTransformer($this->transformer);

        Phake::verify($this->builder)->add('check_list', 'collection', array(
                'entry_type' => 'checkbox',
                'label' => false,
        ));
    }

    /**
     * test Name
     */
    public function testGetName()
    {
        $this->assertEquals('oo_check_list', $this->form->getName());
    }
}
