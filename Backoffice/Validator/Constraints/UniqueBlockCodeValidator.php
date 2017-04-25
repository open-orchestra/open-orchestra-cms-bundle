<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Repository\BlockRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UniqueBlockCodeValidator
 */
class UniqueBlockCodeValidator extends ConstraintValidator
{
    protected $repository;

    /**
     * @param BlockRepositoryInterface $repository
     */
    public function __construct(BlockRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param string         $value
     * @param Constraint     $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        $block = $constraint->block;
        if ($block instanceof BlockInterface && true === $block->isTransverse() && !empty($value)) {
            $blockCode = $this->repository->findOneTransverseBlockByCode($value, $block->getLanguage(), $block->getSiteId());
            if (null !== $blockCode && $blockCode->getId() !== $block->getId()) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('code')
                    ->addViolation();
            }
        }

    }
}
