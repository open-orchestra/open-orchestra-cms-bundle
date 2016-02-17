<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Twig_Environment;
use Twig_Error_Syntax;

/**
 * Class ContentTemplateValidator
 */
class ContentTemplateValidator extends ConstraintValidator
{
    protected $twig;

    /**
     * @param Twig_Environment $twig
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param string $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        try {
            $this->twig->parse($this->twig->tokenize($value));
        } catch (Twig_Error_Syntax $e) {

            $this->context->buildViolation($constraint->message)
                ->atPath('contentTemplate')
                ->addViolation();
        }
    }
}
