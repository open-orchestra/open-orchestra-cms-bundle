<?php

namespace OpenOrchestra\BackOffice\Tests\DisplayBlock;

use OpenOrchestra\Backoffice\DisplayBlock\DisplayBlockManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

class DisplayBlockManagerTest extends AbstractBaseTestCase
{
    /**
     * @var DisplayBlockManager
     */
    protected $manager;

    protected $block;
    protected $strategy;
    protected $templating;
    protected $defaultStrategy;
    protected $blockComponentTag = 'block-component';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->block = Phake::mock('OpenOrchestra\ModelInterface\Model\ReadBlockInterface');

        $this->templating = Phake::mock('Symfony\Component\Templating\EngineInterface');

        $this->defaultStrategy = Phake::mock('OpenOrchestra\Backoffice\DisplayBlock\DisplayBlockInterface');
        $this->strategy = Phake::mock('OpenOrchestra\Backoffice\DisplayBlock\DisplayBlockInterface');
        Phake::when($this->strategy)->support(Phake::anyParameters())->thenReturn(true);

        $this->manager = new DisplayBlockManager(
            $this->templating,
            $this->defaultStrategy
        );
        $this->manager->addStrategy($this->strategy);
    }

    /**
     * Test get templating
     */
    public function testGetTemplating()
    {
        $this->assertSame($this->templating, $this->manager->getTemplating());
    }

    /**
     * Test show
     */
    public function testShow()
    {
        $this->manager->show($this->block);

        Phake::verify($this->defaultStrategy, Phake::never())->show(Phake::anyParameters());
        Phake::verify($this->strategy)->show(Phake::anyParameters());
    }

    /**
     * Test default strategy
     */
    public function testDefaultStrategy()
    {
        Phake::when($this->strategy)->support(Phake::anyParameters())->thenReturn(false);
        $this->manager->show($this->block);

        Phake::verify($this->defaultStrategy)->show(Phake::anyParameters());
        Phake::verify($this->strategy, Phake::never())->show(Phake::anyParameters());
    }
}
