<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type\Component;

use OpenOrchestra\Backoffice\Form\Type\Component\TreeListType;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class TreeListTypeTest
 */
class TreeListTypeTest extends AbstractBaseTestCase
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
        $this->form = new TreeListType($this->transformer);
    }

    /**
     * Test model transformer
     */
    public function testBuildForm()
    {
        $this->form->buildForm($this->builder, array());

        Phake::verify($this->builder)->addModelTransformer($this->transformer);

        Phake::verify($this->builder)->add('tree_list', 'collection', array(
                'entry_type' => 'checkbox',
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
        $this->assertEquals('oo_tree_list', $this->form->getName());
    }
}
