<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type\extension;

use OpenOrchestra\Backoffice\Form\Type\Extension\CollectionExtension;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class UserTypeTest
 */
class CollectionExtensionTest extends AbstractBaseTestCase
{
    /**
     * @var CollectionExtension
     */
    protected $formExtension;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->formExtension = new CollectionExtension();
    }

    /**
     * Test buildForm
     */
    public function testBuildForm()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilderInterface');
        $this->formExtension->buildForm($builder, array(
            'sortable' => true,
       ));
        Phake::verify($builder)->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * Test buildView
     */
    public function testBuildView()
    {
        $formInterface = Phake::mock('Symfony\Component\Form\FormInterface');
        $formView = Phake::mock('Symfony\Component\Form\FormView');

        $this->formExtension->buildView($formView, $formInterface, array('sortable' => true));

        $this->assertEquals($formView->vars['attr']['class'], 'collection-sortable');
    }

    /**
     * Test getExtendedType
     */
    public function testGetExtendedType()
    {
        $this->assertSame('collection', $this->formExtension->getExtendedType());
    }

    /**
     * Test setDefaultOptions
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');
        $this->formExtension->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'sortable' => false,
        ));
    }
}
