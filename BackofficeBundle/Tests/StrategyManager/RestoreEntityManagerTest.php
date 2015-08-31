<?php

namespace OpenOrchestra\BackofficeBundle\Tests\StrategyManager;

use OpenOrchestra\BackofficeBundle\StrategyManager\RestoreEntityManager;
use Phake;

/**
 * Class RestoreEntityManagerTest
 */
class RestoreEntityManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RestoreEntityManager
     */
    protected $manager;
    protected $strategy1;
    protected $strategy2;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->strategy1 = Phake::mock('OpenOrchestra\Backoffice\RestoreEntity\RestoreEntityInterface');
        Phake::when($this->strategy1)->getName()->thenReturn('strategy1');
        Phake::when($this->strategy1)->support(Phake::anyParameters())->thenReturn(true);

        $this->strategy2 = Phake::mock('OpenOrchestra\Backoffice\RestoreEntity\RestoreEntityInterface');
        Phake::when($this->strategy2)->getName()->thenReturn('strategy2');
        Phake::when($this->strategy2)->support(Phake::anyParameters())->thenReturn(false);

        $this->manager = new RestoreEntityManager();
        $this->manager->addStrategy($this->strategy2);
        $this->manager->addStrategy($this->strategy1);
    }

    /**
     * Test restore
     */
    public function testRestore()
    {
        $this->manager->restore(Phake::anyParameters());

        Phake::verify($this->strategy1)->support(Phake::anyParameters());
        Phake::verify($this->strategy2)->support(Phake::anyParameters());

        phake::verify($this->strategy1)->restore(Phake::anyParameters());
        phake::verify($this->strategy2, Phake::never())->restore(Phake::anyParameters());
    }
}
