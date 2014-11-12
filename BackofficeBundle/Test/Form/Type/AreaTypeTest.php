<?php

namespace PHPOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use PHPOrchestra\BackofficeBundle\Form\Type\AreaType;

/**
 * Description of AreaTypeTest
 */
class AreaTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $areaType;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        $this->areaType = new AreaType();
    }

    /**
     * test the build form
     */
    public function testBuildForm()
    {
        $formBuilderMock = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($formBuilderMock)->add(Phake::anyParameters())->thenReturn($formBuilderMock);
        Phake::when($formBuilderMock)->create(Phake::anyParameters())->thenReturn($formBuilderMock);
        Phake::when($formBuilderMock)->addViewTransformer(Phake::anyParameters())->thenReturn($formBuilderMock);

        $this->areaType->buildForm($formBuilderMock, array());

        Phake::verify($formBuilderMock, Phake::times(5))->add(Phake::anyParameters());

        Phake::verify($formBuilderMock, Phake::times(2))->create(Phake::anyParameters());
        Phake::verify($formBuilderMock, Phake::times(2))->addViewTransformer(Phake::anyParameters());

        Phake::verify($formBuilderMock, Phake::times(2))->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * Test the default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->areaType->setDefaultOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'data_class' => 'PHPOrchestra\ModelBundle\Document\Area'
        ));
    }

    /**
     * Test the form name
     */
    public function testGetName()
    {
        $this->assertEquals('area', $this->areaType->getName());
    }
}
