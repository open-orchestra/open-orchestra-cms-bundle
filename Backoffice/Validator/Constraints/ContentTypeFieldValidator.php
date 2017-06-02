<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ContentTypeFieldValidator
 */
class ContentTypeFieldValidator extends ConstraintValidator
{

    protected $disallowedFieldNames;

    /**
     * @param array $disallowedFieldNames
     */
    public function __construct(array $disallowedFieldNames)
    {
        $this->disallowedFieldNames = $disallowedFieldNames;
    }

    /**
     * @param string     $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if($value instanceof ContentTypeInterface) {
            $fields = $value->getFields();
            foreach ($fields as $key => $field) {
                if ($field instanceof FieldTypeInterface && in_array($field->getFieldId(), $this->disallowedFieldNames)) {
                    $this->context->buildViolation($constraint->message)
                        ->atPath('fields[' . $key . '].fieldId')
                        ->addViolation();
                }
            }
        }
    }
}
