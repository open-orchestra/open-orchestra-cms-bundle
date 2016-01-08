<?php

namespace OpenOrchestra\BackofficeBundle\Tests\StrategyManager;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;

/**
 * Class GenerateFormManagerTest
 */
class GenerateFormManagerTest extends AbstractBaseTestCase
{
    /**
     * @var GenerateFormManager
     */
    protected $manager;
    protected $exceptionManager;

    protected $strategy1;
    protected $strategy2;
    protected $block;
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->strategy1 = Phake::mock('OpenOrchestra\Backoffice\GenerateForm\GenerateFormInterface');
        Phake::when($this->strategy1)->getName()->thenReturn('strategy1');
        Phake::when($this->strategy1)->support(Phake::anyParameters())->thenReturn(true);

        Phake::when($this->strategy1)->getRequiredUriParameter()->thenReturn(array());
        Phake::when($this->strategy1)->getDefaultConfiguration()->thenReturn(array());

        $this->strategy2 = Phake::mock('OpenOrchestra\Backoffice\GenerateForm\GenerateFormInterface');
        Phake::when($this->strategy2)->getName()->thenReturn('strategy2');
        Phake::when($this->strategy2)->support(Phake::anyParameters())->thenReturn(false);
        $this->block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');

        $this->form = Phake::mock('Symfony\Component\Form\FormBuilderInterface');

        $this->manager = new GenerateFormManager();
        $this->manager->addStrategy($this->strategy1);
        $this->manager->addStrategy($this->strategy2);

        $this->exceptionManager = new GenerateFormManager();
    }

    /**
     * Test getDefaultConfiguration
     */
    public function testGetDefaultConfiguration()
    {
        $this->manager->getDefaultConfiguration($this->block);

        Phake::verify($this->strategy1)->getDefaultConfiguration();
        Phake::verify($this->strategy2, Phake::never())->getDefaultConfiguration();
    }

    /**
     * Test getDefaultConfiguration with Exception
     *
     * @expectedException OpenOrchestra\Backoffice\Exception\MissingGenerateFormStrategyException
     */
    public function testGetDefaultConfigurationWithException()
    {
        $this->exceptionManager->getDefaultConfiguration($this->block);
    }

    /**
     * Test getRequiredUriParameter
     */
    public function testGetRequiredUriParameter()
    {
        $this->manager->getRequiredUriParameter($this->block);

        Phake::verify($this->strategy1)->getRequiredUriParameter();
        Phake::verify($this->strategy2, Phake::never())->getRequiredUriParameter();
    }

    /**
     * Test getRequiredUriParameter with Exception
     *
     * @expectedException OpenOrchestra\Backoffice\Exception\MissingGenerateFormStrategyException
     */
    public function testGetRequiredUriParameterWithException()
    {
        $this->exceptionManager->getRequiredUriParameter($this->block);
    }

    /**
     * Test Create form
     */
    public function testCreateForm()
    {
        $strategy = $this->manager->createForm($this->block);
        $this->assertSame($this->strategy1, $strategy);
    }

    /**
     * Test Create form with Exception
     *
     * @expectedException OpenOrchestra\Backoffice\Exception\MissingGenerateFormStrategyException
     */
    public function testCreateFormWithException()
    {
        $this->exceptionManager->createForm($this->block);
    }

    /**
     * Test get template
     */
    public function testGetTemplate()
    {
        $this->manager->getTemplate($this->block);

        Phake::verify($this->strategy1)->getTemplate();
        Phake::verify($this->strategy2, Phake::never())->getTemplate();
    }

    /**
     * Test get template with Exception
     *
     * @expectedException OpenOrchestra\Backoffice\Exception\MissingGenerateFormStrategyException
     */
    public function testGetTemplateWithException()
    {
        $this->exceptionManager->getTemplate($this->block);
    }
}
