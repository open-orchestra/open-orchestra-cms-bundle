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
    protected $objectManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        $this->formBuilder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->formBuilder)->addModelTransformer(Phake::anyParameters())->thenReturn($this->formBuilder);
        Phake::when($this->formBuilder)->add(Phake::anyParameters())->thenReturn($this->formBuilder);
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        $this->templateType = new TemplateType($this->templateClass, $this->areaClass, $this->translator, $this->objectManager);
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
        $formInterface = Phake::mock('Symfony\Component\Form\FormInterface');
        $formView = Phake::mock('Symfony\Component\Form\FormView');
        $area = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($area)->getAreaId()->thenReturn($areaId);
        $areaContainer = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaContainerInterface');
        Phake::when($this->objectManager)->refresh($areaContainer)->thenReturn($areaContainer);
        Phake::when($areaContainer)->getAreas()->thenReturn(array($area, $area));
        $formView->vars['value'] = $areaContainer;

        $this->templateType->buildView($formView, $formInterface, array());
        $this->assertEquals($formView->vars['areas'], array($areaId, $areaId));
    }
}
