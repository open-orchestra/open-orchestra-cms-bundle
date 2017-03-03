<?php

namespace OpenOrchestra\BackofficeBundle\Tests\StrategyManager;

use OpenOrchestra\BackofficeBundle\StrategyManager\TrashcanEntityManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class TrashcanEntityManagerTest
 */
class TrashcanEntityManagerTest extends AbstractBaseTestCase
{
    /**
     * @var TrashcanEntityManager
     */
    protected $manager;
    protected $strategy1;
    protected $strategy2;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->strategy1 = Phake::mock('OpenOrchestra\Backoffice\TrashcanEntity\TrashCanEntityInterface');
        Phake::when($this->strategy1)->getName()->thenReturn('strategy1');
        Phake::when($this->strategy1)->support(Phake::anyParameters())->thenReturn(true);

        $this->strategy2 = Phake::mock('OpenOrchestra\Backoffice\TrashcanEntity\TrashCanEntityInterface');
        Phake::when($this->strategy2)->getName()->thenReturn('strategy2');
        Phake::when($this->strategy2)->support(Phake::anyParameters())->thenReturn(false);

        $this->manager = new TrashcanEntityManager();
        $this->manager->addStrategy($this->strategy2);
        $this->manager->addStrategy($this->strategy1);
    }

    /**
     * Test restore
     */
    public function testRemove()
    {
        $trashItem = Phake::mock('OpenOrchestra\ModelInterface\Model\TrashItemInterface');
        $this->manager->remove($trashItem);

        Phake::verify($this->strategy1)->support(Phake::anyParameters());
        Phake::verify($this->strategy2)->support(Phake::anyParameters());

        phake::verify($this->strategy1)->remove(Phake::anyParameters());
        phake::verify($this->strategy2, Phake::never())->remove(Phake::anyParameters());
    }
}
