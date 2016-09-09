<?php

namespace OpenOrchestra\Backoffice\Tests\Reference;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\Backoffice\Reference\ReferenceManager;
use Phake;

/**
 * class ReferenceManagerTest
 */
class ReferenceManagerTest extends AbstractBaseTestCase
{
    protected $manager;
    protected $objectManager;
    protected $strategies;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        $strategy1 = Phake::mock('OpenOrchestra\Backoffice\Reference\Strategies\ReferenceStrategyInterface');
        $strategy2 = Phake::mock('OpenOrchestra\Backoffice\Reference\Strategies\ReferenceStrategyInterface');
        $strategy3 = Phake::mock('OpenOrchestra\Backoffice\Reference\Strategies\ReferenceStrategyInterface');
        $this->strategies = array($strategy1, $strategy2, $strategy3);

        $this->manager = new ReferenceManager($this->objectManager);
        foreach ($this->strategies as $strategy) {
            $this->manager->addStrategy($strategy);
        }
    }

    /**
     * test AddReferencesToEntity
     */
    public function testAddReferencesToEntity($withPreDelete = false)
    {
        $entity = 'fakeEntity';

        $this->manager->addReferencesToEntity($entity);

        foreach ($this->strategies as $strategy) {
            Phake::verify($strategy)->addReferencesToEntity($entity);
        }

        $flushCount = 1;
        if ($withPreDelete) {
            $flushCount = 2;
        }
        Phake::verify($this->objectManager, Phake::times($flushCount))->flush();
    }

    /**
     * test removeReferencesToEntity
     */
    public function testRemoveReferencesToEntity()
    {
        $entity = 'fakeEntity';

        $this->manager->removeReferencesToEntity($entity);

        foreach ($this->strategies as $strategy) {
            Phake::verify($strategy)->removeReferencesToEntity($entity);
        }

        Phake::verify($this->objectManager)->flush();
    }

    /**
     * test updateReferencesToEntity
     */
    public function testUpdateReferencesToEntity()
    {
        $entity = 'fakeEntity';

        $this->testRemoveReferencesToEntity($entity);
        $this->testAddReferencesToEntity($entity, true);
    }
}
