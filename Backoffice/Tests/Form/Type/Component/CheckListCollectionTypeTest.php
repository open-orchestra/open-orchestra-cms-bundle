<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type\Component;

use OpenOrchestra\Backoffice\Form\Type\Component\CheckListCollectionType;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class CheckListCollectionTypeTest
 */
class CheckListCollectionTypeTest extends AbstractBaseTestCase
{
    protected $form;
    protected $transformer;
    protected $builder;
    protected $default = 'default';
    protected $propertyPath = 'propertyPath';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        $this->transformer = Phake::mock('Symfony\Component\Form\DataTransformerInterface');
        $this->form = new CheckListCollectionType($this->transformer);
    }

    /**
     * Test model transformer
     */
    public function testBuildForm()
    {
        $this->form->buildForm($this->builder, array());

        Phake::verify($this->builder)->addModelTransformer($this->transformer);

        Phake::verify($this->builder)->add('check_list_collection', 'collection', array(
                'entry_type' => 'oo_check_list',
                'label' => false,
         ));
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
                'configuration' => array(),
                'max_columns' => 0,
        ));
    }

    /**
     * @param $name
     * @param $label
     *
     * @dataProvider providePropertyPath
     */
    public function testBuildView($propertyPath, $expectedResult)
    {
        $formInterface = Phake::mock('Symfony\Component\Form\FormInterface');
        $formView = Phake::mock('Symfony\Component\Form\FormView');
        $options = array(
            'configuration' => array('default' => $this->default, 'fakePropertyPath' => $this->propertyPath),
            'property_path' => $propertyPath,
            'max_columns' => 0,
        );

        $this->form->buildView($formView, $formInterface, $options);
        $this->assertEquals($expectedResult, $formView->vars['configuration']);
    }

    /**
     * @return array
     */
    public function providePropertyPath()
    {
        return array(
            array(null, $this->default),
            array('[fakePropertyPath]', $this->propertyPath),
        );
    }

    /**
     * test Name
     */
    public function testGetName()
    {
        $this->assertEquals('oo_check_list_collection', $this->form->getName());
    }
}
