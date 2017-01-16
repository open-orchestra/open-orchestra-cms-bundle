<?php

namespace OpenOrchestra\Workflow\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class WorkflowParameterValidator
 */
class WorkflowParameterValidator extends ConstraintValidator
{
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param array      $value      Array of altered statuses
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        $parameters = array(
            'initial_state' => 0,
            'translation_state' => 0,
            'published_state' => 0,
            'auto_publish_from_state' => 0,
            'auto_unpublish_to_state' => 0,
        );

        foreach ($value as $status) {
            if ($status->isInitialState()) {
                $parameters['initial_state']++;
            }
            if ($status->isTranslationState()) {
                $parameters['translation_state']++;
            }
            if ($status->isPublishedState()) {
                $parameters['published_state']++;
            }
            if ($status->isAutoPublishFromState()) {
                $parameters['auto_publish_from_state']++;
            }
            if ($status->isAutoUnpublishToState()) {
                $parameters['auto_unpublish_to_state']++;
            }
        }

        foreach ($parameters as $parameter => $setCount) {
            if (0 === $setCount) {
                $label = $this->translator
                    ->trans('open_orchestra_workflow_admin.status.' . $parameter, array(), 'interface');
                $this->context
                    ->buildViolation($constraint->requiredParameterMessage)
                    ->setParameter('%parameter%', $label)
                    ->addViolation();
            } elseif ($setCount > 1) {
                $label = $this->translator
                    ->trans('open_orchestra_workflow_admin.status.' . $parameter, array(), 'interface');
                $this->context
                    ->buildViolation($constraint->uniqueParameterMessage)
                    ->setParameter('%parameter%', $label)
                    ->addViolation();
            }
        }
    }
}
