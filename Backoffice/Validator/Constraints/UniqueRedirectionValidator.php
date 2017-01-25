<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use OpenOrchestra\ModelInterface\Repository\RedirectionRepositoryInterface;

/**
 * Class UniqueRedirectionValidator
 */
class UniqueRedirectionValidator extends ConstraintValidator
{
    protected $repository;

    /**
     * @param RedirectionRepositoryInterface $repository
     */
    public function __construct(RedirectionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param array      $value      Array of altered statuses
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if ('' !== $value->getRoutePattern()
            && $this->repository->countByPattern($value->getRoutePattern(), $value->getId()) > 0
        ) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('route_pattern')
                ->addViolation();
        }
    }
}
