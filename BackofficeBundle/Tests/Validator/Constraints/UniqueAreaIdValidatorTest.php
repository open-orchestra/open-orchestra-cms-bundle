<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Validator\Constraints;

use OpenOrchestra\BackofficeBundle\Validator\Constraints\UniqueAreaIdValidator;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class UniqueAreaIdValidatorTest
 */
class UniqueAreaIdValidatorTest extends AbstractBaseTestCase
{
    /**
     * @var UniqueAreaIdValidator
     */
    protected $validator;

    protected $constraint;
    protected $context;
    protected $area2;
    protected $area1;
    protected $areaContainer;
    protected $areaId = 'fakeId';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = Phake::mock('Symfony\Component\Validator\Constraint');
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContextInterface');
        $constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');

        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($constraintViolationBuilder);
        Phake::when($constraintViolationBuilder)->atPath(Phake::anyParameters())->thenReturn($constraintViolationBuilder);

        $area = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($area)->getAreaId()->thenReturn($this->areaId);
        $this->area1 = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        $this->area2 = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');

        $areas = array();
        $areas[] = $area;
        $areas[] = $this->area1;
        $areas[] = $this->area2;

        $this->areaContainer = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaContainerInterface');
        Phake::when($this->areaContainer)->getAreas()->thenReturn($areas);

        $this->validator = new UniqueAreaIdValidator();
        $this->validator->initialize($this->context);
    }

    /**
     * Test instance
     */
    public function testClass()
    {
        $this->assertInstanceOf('Symfony\Component\Validator\ConstraintValidator', $this->validator);
    }

    /**
     * @param string  $areaId
     * @param string  $areaId2
     * @param integer $violationTimes
     *
     * @dataProvider provideAreaIdAndViolation
     */
    public function testValidate($areaId, $areaId2, $violationTimes)
    {
        Phake::when($this->area1)->getAreaId()->thenReturn($areaId);
        Phake::when($this->area2)->getAreaId()->thenReturn($areaId2);

        $this->validator->validate($this->areaContainer, $this->constraint);

        Phake::verify($this->context, Phake::times($violationTimes))->buildViolation(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideAreaIdAndViolation()
    {
        return array(
            array('fakeId2','fakeId3', 0),
            array('fakeId','fakeId3', 1),
            array('fakeId','fakeId', 2),
        );
    }
}
