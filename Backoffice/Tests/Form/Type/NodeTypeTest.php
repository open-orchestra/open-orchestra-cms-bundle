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
    protected $templateManager;
    protected $contextManager;
    protected $siteRepository;
    protected $nodeClass = 'nodeClass';
    protected $specialPageChoiceSubscriber;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        $this->nodeManager = Phake::mock('OpenOrchestra\Backoffice\Manager\NodeManager');
        $this->contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface');
        $this->templateManager = Phake::mock('OpenOrchestra\Backoffice\Manager\TemplateManager');

        $this->specialPageChoiceSubscriber = Phake::mock('Symfony\Component\EventDispatcher\EventSubscriberInterface');

        $this->nodeType = new NodeType(
            $this->nodeManager,
            $this->contextManager,
            $this->siteRepository,
            $this->templateManager,
            $this->nodeClass,
            $this->specialPageChoiceSubscriber,
            array()
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
        Phake::verify($formBuilderMock, Phake::times(17))->add(Phake::anyParameters());

        Phake::verify($formBuilderMock, Phake::never())->addModelTransformer(Phake::anyParameters());
        Phake::verify($formBuilderMock, Phake::times(2))->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * Test build form with front role
     */
    public function testBuildFormWithFrontRole()
    {
        $form = new NodeType(
            $this->nodeManager,
            $this->contextManager,
            $this->siteRepository,
            $this->templateManager,
            $this->nodeClass,
            $this->specialPageChoiceSubscriber,
            array('fakeRole' => 'fakeLabel')
        );
        $formBuilderMock = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($formBuilderMock)->add(Phake::anyParameters())->thenReturn($formBuilderMock);

        $form->buildForm($formBuilderMock, array());
        Phake::verify($formBuilderMock, Phake::times(18))->add(Phake::anyParameters());
    }

    /**
     * Test configureOptions
     */
    public function testConfigureOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->nodeType->configureOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'data_class' => $this->nodeClass,
            'delete_button' => false,
            'enable_delete_button' => false,
            'delete_help_text' => 'open_orchestra_backoffice.form.node.delete_help_text',
            'group_enabled' => true,
            'group_render' => array(
                'properties' => array(
                    'rank' => 0,
                    'label' => 'open_orchestra_backoffice.form.node.group.properties',
                ),
                'seo' => array(
                    'rank' => 1,
                    'label' => 'open_orchestra_backoffice.form.node.group.seo',
                ),
                'keywords' => array(
                    'rank' => 2,
                    'label' => 'open_orchestra_backoffice.form.node.group.keywords',
                ),
                'cache' => array(
                    'rank' => 3,
                    'label' => 'open_orchestra_backoffice.form.node.group.cache',
                ),
            ),
            'sub_group_render' => array(
                'properties' => array(
                    'rank' => 0,
                    'label' => 'open_orchestra_backoffice.form.node.sub_group.properties',
                ),
                'style' => array(
                    'rank' => 1,
                    'label' => 'open_orchestra_backoffice.form.node.sub_group.style',
                ),
                'publication' => array(
                    'rank' => 2,
                    'label' => 'open_orchestra_backoffice.form.node.sub_group.publication',
                ),
                'seo' => array(
                    'rank' => 0,
                    'label' => 'open_orchestra_backoffice.form.node.sub_group.seo',
                ),
                'canonical' => array(
                    'rank' => 1,
                    'label' => 'open_orchestra_backoffice.form.node.sub_group.canonical',
                ),
                'keywords' => array(
                    'rank' => 1,
                    'label' => 'open_orchestra_backoffice.form.node.sub_group.keywords',
                ),
                'cache' => array(
                    'rank' => 0,
                    'label' => 'open_orchestra_backoffice.form.node.sub_group.cache',
                )
            ),
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
