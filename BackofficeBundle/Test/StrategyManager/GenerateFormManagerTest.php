<?php

namespace PHPOrchestra\BackofficeBundle\Test\StrategyManager;

use Phake;
use PHPOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;

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
        $this->strategy1 = Phake::mock('PHPOrchestra\Backoffice\GenerateForm\GenerateFormInterface');
        Phake::when($this->strategy1)->getName()->thenReturn('strategy1');
        Phake::when($this->strategy1)->support(Phake::anyParameters())->thenReturn(true);
        $this->strategy2 = Phake::mock('PHPOrchestra\Backoffice\GenerateForm\GenerateFormInterface');
        Phake::when($this->strategy2)->getName()->thenReturn('strategy2');
        Phake::when($this->strategy2)->support(Phake::anyParameters())->thenReturn(false);
        $this->block = Phake::mock('PHPOrchestra\ModelInterface\Model\BlockInterface');
        $this->form = Phake::mock('Symfony\Component\Form\Form');

        $this->manager = new GenerateFormManager();
        $this->manager->addStrategy($this->strategy1);
        $this->manager->addStrategy($this->strategy2);
    }

    /**
     * Test build form
     */
    public function testBuildForm()
    {
        $this->manager->buildForm($this->form, $this->block);

        Phake::verify($this->strategy1)->buildForm($this->form, $this->block);
        Phake::verify($this->strategy2, Phake::never())->buildForm($this->form, $this->block);
    }

    /**
     * Test alter form
     */
    public function testAlterFormAfterSubmit()
    {
        $this->manager->alterFormAfterSubmit($this->form, $this->block);

        Phake::verify($this->strategy1)->alterFormAfterSubmit($this->form, $this->block);
        Phake::verify($this->strategy2, Phake::never())->alterFormAfterSubmit($this->form, $this->block);
    }
}
