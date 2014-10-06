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
        $this->areaType = new areaType();
    }

    /**
     * test the build form
     *
     * @param array $options
     * @param int $expectedCount
     *
     * @dataProvider getOptions
     */
    public function testBuildForm($options, $expectedCount)
    {
        $formBuilderMock = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($formBuilderMock)->create(Phake::anyParameters())->thenReturn($formBuilderMock);
        
        $this->areaType->buildForm($formBuilderMock, $options);

        Phake::verify($formBuilderMock, Phake::times($expectedCount))->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * Test the default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->areaType->setDefaultOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'data_class' => 'PHPOrchestra\ModelBundle\Document\Area',
            'node' => null
        ));
    }

    /**
     * Test the form name
     */
    public function testGetName()
    {
        $this->assertEquals('area', $this->areaType->getName());
    }
    
    /**
     * Options provider
     *
     * @return array
     */
    public function getOptions()
    {
        return array(
            array(
                array('fakeKey' => 'fakeValue'), 2
            ),
            array(
                array(
                    'node' => Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface')
                ),
                3
            )
        );
    }
}
