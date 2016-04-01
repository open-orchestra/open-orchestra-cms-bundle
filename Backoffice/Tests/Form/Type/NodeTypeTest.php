<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\Type\NodeType;

/**
 * Description of NodeTypeTest
 */
class NodeTypeTest extends AbstractBaseTestCase
{
    protected $nodeType;
    protected $nodeManager;
    protected $templateRepository;
    protected $siteRepository;
    protected $nodeClass = 'nodeClass';
    protected $areaClass = 'areaClass';
    protected $translator;
    protected $objectManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        $this->templateRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\TemplateRepositoryInterface');
        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        $this->nodeManager = Phake::mock('OpenOrchestra\Backoffice\Manager\NodeManager');
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        $this->nodeType = new NodeType(
            $this->nodeClass,
            $this->templateRepository,
            $this->siteRepository,
            $this->nodeManager,
            $this->areaClass,
            $this->translator,
            $this->objectManager
        );
    }

    /**
     * test build form
     */
    public function testBuildForm()
    {
        $formBuilderMock = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($formBuilderMock)->add(Phake::anyParameters())->thenReturn($formBuilderMock);

        $this->nodeType->buildForm($formBuilderMock, array());

        Phake::verify($formBuilderMock, Phake::times(15))->add(Phake::anyParameters());

        Phake::verify($formBuilderMock, Phake::never())->addModelTransformer(Phake::anyParameters());
        Phake::verify($formBuilderMock, Phake::times(4))->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * Test configureOptions
     */
    public function testConfigureOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->nodeType->configureOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'data_class' => $this->nodeClass
        ));
    }

    /**
     * Test the form name
     */
    public function testGetName()
    {
        $this->assertEquals('oo_node', $this->nodeType->getName());
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

        $this->nodeType->buildView($formView, $formInterface, array());
        $this->assertEquals($formView->vars['areas'], array($areaId, $areaId));
        $this->assertEquals($formView->vars['form_legend_helper'], "open_orchestra_backoffice.form.node.template_selection.helper");
    }
}
