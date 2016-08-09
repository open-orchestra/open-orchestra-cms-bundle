<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type;

use OpenOrchestra\Backoffice\Form\Type\AreaColumnType;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class AreaColumnTypeTest
 */
class AreaColumnTypeTest extends AbstractBaseTestCase
{
    protected $areaClass = 'OpenOrchestra\ModelBundle\Document\AreaFlex';
    /** @var  AreaColumnType */
    protected $areaType;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->areaType = new AreaColumnType($this->areaClass);
    }

    /**
     * test the build form
     */
    public function testBuildForm()
    {
        $formBuilderMock = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($formBuilderMock)->add(Phake::anyParameters())->thenReturn($formBuilderMock);

        $this->areaType->buildForm($formBuilderMock, array());

        Phake::verify($formBuilderMock, Phake::times(3))->add(Phake::anyParameters());
    }

    /**
     * Test the default options
     */
    public function testConfigureOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->areaType->configureOptions($resolverMock);
        Phake::verify($resolverMock)->setDefault('data_class', $this->areaClass);
    }
}
