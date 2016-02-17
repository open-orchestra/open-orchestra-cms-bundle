<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\NodeType;

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

    /**
     * Set up the test
     */
    public function setUp()
    {
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
            $this->translator
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
        Phake::verify($formBuilderMock, Phake::times(3))->addEventSubscriber(Phake::anyParameters());
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
}
