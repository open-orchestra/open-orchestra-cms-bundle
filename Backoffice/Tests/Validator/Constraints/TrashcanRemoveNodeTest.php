<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\Backoffice\Validator\Constraints\TrashcanRemoveNode;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Symfony\Component\Validator\Constraint;

/**
 * Class TrashcanRemoveNodeTest
 */
class TrashcanRemoveNodeTest extends AbstractBaseTestCase
{
    /**
     * @var TrashcanRemoveNode
     */
    protected $constraint;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new TrashcanRemoveNode();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Validator\Constraint', $this->constraint);
    }

    /**
     * Test target
     */
    public function testTarget()
    {
        $this->assertSame(Constraint::CLASS_CONSTRAINT, $this->constraint->getTargets());
    }

    /**
     * Test messages
     */
    public function testMessages()
    {
        $this->assertSame('open_orchestra_backoffice_validators.trashitem.remove_node_date', $this->constraint->message);
    }

    /**
     * Test validate by
     */
    public function testValidateBy()
    {
        $this->assertSame('remove_node_date', $this->constraint->validatedBy());
    }
}
