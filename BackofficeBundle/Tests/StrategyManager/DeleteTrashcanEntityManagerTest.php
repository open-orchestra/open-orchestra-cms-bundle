<?php

namespace OpenOrchestra\BackofficeBundle\Tests\StrategyManager;

use OpenOrchestra\BackofficeBundle\StrategyManager\DeleteTrashcanEntityManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class DeleteTrashcanEntityManagerTest
 */
class DeleteTrashcanEntityManagerTest extends AbstractBaseTestCase
{
    /**
     * @var DeleteTrashcanEntityManager
     */
    protected $manager;
    protected $strategy1;
    protected $strategy2;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->strategy1 = Phake::mock('OpenOrchestra\Backoffice\DeleteTrashcanEntity\DeleteTrashCanEntityInterface');
        Phake::when($this->strategy1)->getName()->thenReturn('strategy1');
        Phake::when($this->strategy1)->support(Phake::anyParameters())->thenReturn(true);

        $this->strategy2 = Phake::mock('OpenOrchestra\Backoffice\DeleteTrashcanEntity\DeleteTrashCanEntityInterface');
        Phake::when($this->strategy2)->getName()->thenReturn('strategy2');
        Phake::when($this->strategy2)->support(Phake::anyParameters())->thenReturn(false);

        $this->manager = new DeleteTrashcanEntityManager();
        $this->manager->addStrategy($this->strategy2);
        $this->manager->addStrategy($this->strategy1);
    }

    /**
     * Test restore
     */
    public function testDelete()
    {
        $entity = Phake::mock('OpenOrchestra\ModelInterface\Model\SoftDeleteableInterface');
        $this->manager->delete($entity);

        Phake::verify($this->strategy1)->support(Phake::anyParameters());
        Phake::verify($this->strategy2)->support(Phake::anyParameters());

        phake::verify($this->strategy1)->delete(Phake::anyParameters());
        phake::verify($this->strategy2, Phake::never())->delete(Phake::anyParameters());
    }
}
