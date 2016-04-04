<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\Type\TemplateType;

/**
 * Description of TemplateTypeTest
 */
class TemplateTypeTest extends AbstractBaseTestCase
{
    protected $formBuilder;
    protected $templateType;
    protected $nodeTypeTransformer;
    protected $areaClass = 'areaClass';
    protected $templateClass = 'templateClass';
    protected $translator;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->formBuilder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->formBuilder)->addModelTransformer(Phake::anyParameters())->thenReturn($this->formBuilder);
        Phake::when($this->formBuilder)->add(Phake::anyParameters())->thenReturn($this->formBuilder);
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        $this->templateType = new TemplateType($this->templateClass, $this->areaClass, $this->translator);
    }

    /**
     * test Build form method
     */
    public function testBuildForm()
    {
        $this->templateType->buildForm($this->formBuilder, array());

        Phake::verify($this->formBuilder, Phake::never())->addModelTransformer(Phake::anyParameters());
        Phake::verify($this->formBuilder, Phake::times(4))->add(Phake::anyParameters());
        Phake::verify($this->formBuilder, Phake::times(1))->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * test configureOptions
     */
    public function testConfigureOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->templateType->configureOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'data_class' => $this->templateClass,
        ));
    }

    /**
     * test get name
     */
    public function testGetName()
    {
        $this->assertEquals('oo_template', $this->templateType->getName());
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

        $this->templateType->buildView($formView, $formInterface, array());
        $this->assertEquals(array_values($formView->vars['areas']), array($areaId));
    }
}
