<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Validator\Constraints;

use OpenOrchestra\BackofficeBundle\Validator\Constraints\BlockNodePattern;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Symfony\Component\Validator\Constraint;

/**
 * Class BlockNodePatternTest
 */
class BlockNodePatternTest extends AbstractBaseTestCase
{
    /**
     * @var BlockNodePattern
     */
    protected $constraint;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new BlockNodePattern();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Validator\Constraint', $this->constraint);
    }

    /**
     * test target
     */
    public function testTarget()
    {
        $this->assertSame(Constraint::CLASS_CONSTRAINT, $this->constraint->getTargets());
    }

    /**
     * test message
     */
    public function testMessages()
    {
        $this->assertSame('open_orchestra_backoffice_validators.node.pattern', $this->constraint->message);
    }

    /**
     * Test validate by
     */
    public function testValidateBy()
    {
        $this->assertSame('block_node_pattern', $this->constraint->validatedBy());
    }
}
