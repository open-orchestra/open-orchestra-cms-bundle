<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Validator\Constraints;

use OpenOrchestra\BackofficeBundle\Validator\Constraints\AreaFlexRowLayout;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Symfony\Component\Validator\Constraint;

/**
 * Class AreaFlexRowLayoutTest
 */
class AreaFlexRowLayoutTest extends AbstractBaseTestCase
{
    /**
     * @var AreaFlexRowLayout
     */
    protected $constraint;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new AreaFlexRowLayout();
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
        $this->assertSame(Constraint::PROPERTY_CONSTRAINT, $this->constraint->getTargets());
    }

    /**
     * test message
     */
    public function testMessages()
    {
        $this->assertSame('open_orchestra_backoffice_validators.form.area_flex.row_layout', $this->constraint->message);
    }

    /**
     * Test validate by
     */
    public function testValidateBy()
    {
        $this->assertSame('area_flex_row_layout', $this->constraint->validatedBy());
    }
}
