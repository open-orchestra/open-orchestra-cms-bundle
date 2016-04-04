<?php

namespace OpenOrchestra\BackofficeBundle\Tests\StrategyManager;

use OpenOrchestra\BackofficeBundle\StrategyManager\RemoveTrashcanEntityManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class RemoveTrashcanEntityManagerTest
 */
class RemoveTrashcanEntityManagerTest extends AbstractBaseTestCase
{
    /**
     * @var RemoveTrashcanEntityManager
     */
    protected $manager;
    protected $strategy1;
    protected $strategy2;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->strategy1 = Phake::mock('OpenOrchestra\Backoffice\RemoveTrashcanEntity\RemoveTrashCanEntityInterface');
        Phake::when($this->strategy1)->getName()->thenReturn('strategy1');
        Phake::when($this->strategy1)->support(Phake::anyParameters())->thenReturn(true);

        $this->strategy2 = Phake::mock('OpenOrchestra\Backoffice\RemoveTrashcanEntity\RemoveTrashCanEntityInterface');
        Phake::when($this->strategy2)->getName()->thenReturn('strategy2');
        Phake::when($this->strategy2)->support(Phake::anyParameters())->thenReturn(false);

        $this->manager = new RemoveTrashcanEntityManager();
        $this->manager->addStrategy($this->strategy2);
        $this->manager->addStrategy($this->strategy1);
    }

    /**
     * Test restore
     */
    public function testRemove()
    {
        $entity = Phake::mock('OpenOrchestra\ModelInterface\Model\SoftDeleteableInterface');
        $this->manager->delete($entity);

        Phake::verify($this->strategy1)->support(Phake::anyParameters());
        Phake::verify($this->strategy2)->support(Phake::anyParameters());

        phake::verify($this->strategy1)->remove(Phake::anyParameters());
        phake::verify($this->strategy2, Phake::never())->remove(Phake::anyParameters());
    }
}
