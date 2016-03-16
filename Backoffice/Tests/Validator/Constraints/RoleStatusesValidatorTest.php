<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\Backoffice\Validator\Constraints\RoleStatusesValidator;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class RoleStatusesValidatorTest
 */
class RoleStatusesValidatorTest extends AbstractBaseTestCase
{
    /**
     * @var RoleStatusesValidator
     */
    protected $validator;

    protected $constraint;
    protected $context;
    protected $roleId = 'fakeRoleId';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = Phake::mock('Symfony\Component\Validator\Constraint');
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContextInterface');
        $this->role = Phake::mock('OpenOrchestra\ModelInterface\Model\RoleInterface');
        $constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');

        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($constraintViolationBuilder);
        Phake::when($this->role)->getId()->thenReturn($this->roleId);
        Phake::when($this->role)->getFromStatus()->thenReturn(Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface'));
        Phake::when($this->role)->getToStatus()->thenReturn(Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface'));
        Phake::when($constraintViolationBuilder)->atPath(Phake::anyParameters())->thenReturn($constraintViolationBuilder);
    }

    /**
     * Test instance
     */
    public function testClass()
    {
        $roleRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface');
        $validator = new RoleStatusesValidator($roleRepository);
        $this->assertInstanceOf('Symfony\Component\Validator\ConstraintValidator', $validator);
    }

    /**
     * @param string|null $roleId
     * @param integer     $violationTimes
     *
     * @dataProvider provideAreaIdAndViolation
     */
    public function testValidate($roleId, $violationTimes)
    {
        $roleRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface');
        if (!is_null($roleId)) {
            $role = Phake::mock('OpenOrchestra\ModelInterface\Model\RoleInterface');
            Phake::when($role)->getId()->thenReturn($roleId);
            Phake::when($roleRepository)->findOneByFromStatusAndToStatus(Phake::anyParameters())->thenReturn($role);
        } else {
            Phake::when($roleRepository)->findOneByFromStatusAndToStatus(Phake::anyParameters())->thenReturn(null);
        }

        $validator = new RoleStatusesValidator($roleRepository);
        $validator->initialize($this->context);
        $validator->validate($this->role, $this->constraint);

        Phake::verify($this->context, Phake::times($violationTimes))->buildViolation(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideAreaIdAndViolation()
    {
        return array(
            array(null, 0),
            array($this->roleId, 0),
            array('fakeAnotherRoleId', 1),
        );
    }
}
