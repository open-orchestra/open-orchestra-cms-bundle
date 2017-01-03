<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type\Component;

use OpenOrchestra\Backoffice\Form\Type\Component\TreeListCollectionType;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class TreeListCollectionTypeTest
 */
class TreeListCollectionTypeTest extends AbstractBaseTestCase
{
    protected $form;
    protected $transformer;
    protected $builder;
    protected $configuration = array('configuration' => 'fakeConfiguration');

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        $this->transformer = Phake::mock('Symfony\Component\Form\DataTransformerInterface');
        $this->form = new TreeListCollectionType($this->transformer);
    }

    /**
     * Test model transformer
     */
    public function testBuildForm()
    {
        $this->form->buildForm($this->builder, $this->configuration);

        Phake::verify($this->builder)->addModelTransformer($this->transformer);

        Phake::verify($this->builder)->add('tree_list_collection', 'collection', array(
                'entry_type' => 'oo_tree_list',
                'entry_options' => array(
                    'configuration' => $this->configuration['configuration'],
                ),
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
     * test buildView
     */
    public function testBuildView()
    {
        $formInterface = Phake::mock('Symfony\Component\Form\FormInterface');
        $formView = Phake::mock('Symfony\Component\Form\FormView');

        $this->form->buildView($formView, $formInterface, $this->configuration);
        $this->assertEquals($this->configuration['configuration'], $formView->vars['configuration']);
    }

    /**
     * test Name
     */
    public function testGetName()
    {
        $this->assertEquals('oo_tree_list_collection', $this->form->getName());
    }
}
