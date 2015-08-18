<?php

namespace OpenOrchestra\BackofficeBundle\Validator\Constraints;

use OpenOrchestra\BackofficeBundle\StrategyManager\AuthorizeEditionManager;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class AuthorizeEditionValidator
 */
class AuthorizeEditionValidator extends ConstraintValidator
{
    protected $authorizeEditionManager;

    /**
     * @param AuthorizeEditionManager $authorizeEditionManager
     */
    public function __construct(AuthorizeEditionManager $authorizeEditionManager)
    {
        $this->authorizeEditionManager = $authorizeEditionManager;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$this->authorizeEditionManager->isEditable($value)
            && $value instanceof StatusableInterface
            && ! $value->hasStatusChanged()
        ) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
