<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UniqueNodeSpecialPageValidator
 */
class UniqueNodeSpecialPageValidator extends ConstraintValidator
{
    protected $repository;

    /**
     * @param NodeRepositoryInterface $repository
     */
    public function __construct(NodeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param NodeInterface $value      The value that should be validated
     * @param Constraint    $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($value, Constraint $constraint)
    {
        if (null !== $value->getSpecialPageName()) {
            $countSpecialPage = $this->repository->countOtherNodeWithSameSpecialPageName(
                $value->getNodeId(),
                $value->getSiteId(),
                $value->getLanguage(),
                $value->getSpecialPageName()
            );

            if ($countSpecialPage > 0) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('specialPageName')
                    ->addViolation();
            }
        }
    }
}
