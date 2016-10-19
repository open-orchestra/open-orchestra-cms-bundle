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
    protected $contextManager;
    protected $siteRepository;
    protected $nodeClass = 'nodeClass';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        $this->nodeManager = Phake::mock('OpenOrchestra\Backoffice\Manager\NodeManager');
        $this->contextManager = Phake::mock('OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface');

        $this->nodeType = new NodeType(
            $this->nodeManager,
            $this->contextManager,
            $this->siteRepository,
            array(),
            $this->nodeClass
        );
    }

    /**
     * test build form
     */
    public function testBuildForm()
    {
        $formBuilderMock = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($formBuilderMock)->add(Phake::anyParameters())->thenReturn($formBuilderMock);

        $this->nodeType->buildForm($formBuilderMock, array('activateBoLabel' => true));

        Phake::verify($formBuilderMock, Phake::times(18))->add(Phake::anyParameters());

        Phake::verify($formBuilderMock, Phake::never())->addModelTransformer(Phake::anyParameters());
        Phake::verify($formBuilderMock, Phake::times(2))->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * test build without bo label form
     */
    public function testBuildFormWithoutBoLabel()
    {
        $formBuilderMock = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($formBuilderMock)->add(Phake::anyParameters())->thenReturn($formBuilderMock);

        $this->nodeType->buildForm($formBuilderMock, array('activateBoLabel' => false));

        Phake::verify($formBuilderMock, Phake::times(17))->add(Phake::anyParameters());

        Phake::verify($formBuilderMock, Phake::never())->add('boLabel', 'text', array(
            'label' => 'open_orchestra_backoffice.form.node.boLabel.name',
            'attr' => array(
                'class' => 'generate-id-dest',
                'help_text' => 'open_orchestra_backoffice.form.node.boLabel.helper',
            )
        ));
        Phake::verify($formBuilderMock, Phake::never())->addModelTransformer(Phake::anyParameters());
        Phake::verify($formBuilderMock, Phake::times(2))->addEventSubscriber(Phake::anyParameters());
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
            'activateBoLabel' => true,
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
