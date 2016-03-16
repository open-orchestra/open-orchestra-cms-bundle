<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class RoleStatusesValidator
 */
class RoleStatusesValidator extends ConstraintValidator
{
    protected $roleRepository;

    /**
     * @param RoleRepositoryInterface   $roleRepository
     */
    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param RoleInterface $node
     * @param Constraint    $constraint
     */
    public function validate($role, Constraint $constraint)
    {
        $existingRole = $this->roleRepository->findOneByFromStatusAndToStatus($role->getFromStatus(), $role->getToStatus());
        if (!is_null($existingRole) && $existingRole->getId() != $role->getId()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
