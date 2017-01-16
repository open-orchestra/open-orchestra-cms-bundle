<?php

namespace OpenOrchestra\Workflow\Tests\Factory;

use Phake;
use OpenOrchestra\Workflow\Factory\TransitionFactory;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;

/**
 * Class TransitionFactoryTest
 */
class TransitionFactoryTest extends AbstractBaseTestCase
{
    protected $factory;
    protected $transitionClassName = 'OpenOrchestra\ModelInterface\Model\WorkflowTransitionInterface';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->factory = new TransitionFactory($this->transitionClassName);
    }

    public function testCreate()
    {
        $statusFrom = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $statusTo = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');

        $transition = $this->factory->create($statusFrom, $statusTo);

        $this->assertInstanceOf($this->transitionClassName, $transition);
        $this->assertSame($statusFrom, $transition->getStatusFrom());
        $this->assertSame($statusTo, $transition->getStatusTo());
    }
}
