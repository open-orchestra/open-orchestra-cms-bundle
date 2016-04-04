<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\Type\AreaType;

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

    /**
     * test buildView
     */
    public function testBuildView()
    {
        $areaId = 'fakeAreaId';
        $errorFormInterface = Phake::mock('Symfony\Component\Form\FormInterface');
        Phake::when($errorFormInterface)->getData()->thenReturn(array($areaId));

        $error = Phake::mock('Symfony\Component\Form\FormError');
        Phake::when($error)->getOrigin()->thenReturn($errorFormInterface);

        $newAreasInterface = Phake::mock('Symfony\Component\Form\FormInterface');
        Phake::when($newAreasInterface)->getErrors()->thenReturn(array($error));

        $formInterface = Phake::mock('Symfony\Component\Form\FormInterface');
        Phake::when($formInterface)->get('newAreas')->thenReturn($newAreasInterface);

        $formView = Phake::mock('Symfony\Component\Form\FormView');
        $area = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($area)->getAreaId()->thenReturn($areaId);
        $areaContainer = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaContainerInterface');
        Phake::when($areaContainer)->getAreas()->thenReturn(array($area, $area));
        $formView->vars['value'] = $areaContainer;

        $this->areaType->buildView($formView, $formInterface, array());
        $this->assertEquals(array_values($formView->vars['areas']), array($areaId));
    }
}
