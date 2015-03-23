<?php

namespace OpenOrchestra\BackofficeBundle\Validator\Constraints;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Twig_Environment;
use Twig_Error_Syntax;

/**
 * Class ContentTemplateValidator
 */
class ContentTemplateValidator extends ConstraintValidator
{
    protected $translator;
    protected $twig;

    /**
     * @param TranslatorInterface $translator
     * @param Twig_Environment    $twig
     */
    public function __construct(TranslatorInterface $translator, Twig_Environment $twig)
    {
        $this->translator = $translator;
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
            $this->context->addViolationAt('contentTemplate', $this->translator->trans($constraint->message));
        }
    }
}
