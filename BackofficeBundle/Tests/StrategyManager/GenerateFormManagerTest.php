<?php

namespace OpenOrchestra\BackofficeBundle\Tests\StrategyManager;

use Phake;
use OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;

/**
 * Class GenerateFormManagerTest
 */
class GenerateFormManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GenerateFormManager
     */
    protected $manager;

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
        $this->strategy2 = Phake::mock('OpenOrchestra\Backoffice\GenerateForm\GenerateFormInterface');
        Phake::when($this->strategy2)->getName()->thenReturn('strategy2');
        Phake::when($this->strategy2)->support(Phake::anyParameters())->thenReturn(false);
        $this->block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        $this->form = Phake::mock('Symfony\Component\Form\FormBuilderInterface');

        $this->manager = new GenerateFormManager();
        $this->manager->addStrategy($this->strategy1);
        $this->manager->addStrategy($this->strategy2);
    }

    /**
     * Test build form
     */
    public function testBuildForm()
    {
        $this->manager->buildForm($this->form, array(), $this->block);

        Phake::verify($this->strategy1)->buildForm($this->form, array());
        Phake::verify($this->strategy2, Phake::never())->buildForm($this->form, array());
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
     * Test Create form
     */
    public function testCreateForm()
    {
        $strategy = $this->manager->createForm($this->block);
        $this->assertSame($this->strategy1, $strategy);
    }
}
