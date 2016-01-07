<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\AreaType;

/**
 * Description of AreaTypeTest
 */
class AreaTypeTest extends AbstractBaseTestCase
{
    protected $areaType;
    protected $areaClass = 'areaClass';
    protected $translator;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        $this->areaType = new AreaType($this->areaClass, $this->translator);
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

        Phake::verify($formBuilderMock, Phake::times(4))->add(Phake::anyParameters());

        Phake::verify($formBuilderMock, Phake::times(2))->create(Phake::anyParameters());
        Phake::verify($formBuilderMock, Phake::times(2))->addViewTransformer(Phake::anyParameters());

        Phake::verify($formBuilderMock, Phake::times(1))->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * Test the default options
     */
    public function testConfigureOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->areaType->configureOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'data_class' => $this->areaClass,
        ));
    }

    /**
     * Test the form name
     */
    public function testGetName()
    {
        $this->assertEquals('oo_area', $this->areaType->getName());
    }
}
