<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type;

use OpenOrchestra\Backoffice\Form\Type\AreaFlexRowType;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class AreaFlexRowTypeTest
 */
class AreaFlexRowTypeTest extends AbstractBaseTestCase
{
    protected $areaClass = 'OpenOrchestra\ModelBundle\Document\AreaFlex';
    /** @var  AreaFlexRowType */
    protected $areaType;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $areaFlexManager = Phake::mock('OpenOrchestra\BackofficeBundle\Manager\AreaFlexManager');
        $this->areaType = new AreaFlexRowType($this->areaClass, $areaFlexManager);
    }

    /**
     * test the build form
     */
    public function testBuildForm()
    {
        $formBuilderMock = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($formBuilderMock)->add(Phake::anyParameters())->thenReturn($formBuilderMock);

        $this->areaType->buildForm($formBuilderMock, array());

        Phake::verify($formBuilderMock, Phake::times(1))->addEventSubscriber(Phake::anyParameters());
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
