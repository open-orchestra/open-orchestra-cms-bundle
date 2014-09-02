<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\FieldTypeFacade;
use PHPOrchestra\ModelBundle\Document\FieldType;
use PHPOrchestra\ModelBundle\Model\FieldTypeInterface;

/**
 * Class FieldTypeTransformer
 */
class FieldTypeTransformer extends AbstractTransformer
{
    /**
     * @param FieldTypeInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new FieldTypeFacade();

        $facade->fieldId = $mixed->getFieldId();
        $facade->label = $mixed->getLabel();
        $facade->defaultValue = $mixed->getDefaultValue();
        $facade->searchable = $mixed->getSearchable();
        $facade->type = $mixed->getType();

        foreach ($mixed->getOptions() as $option) {
            $facade->addOption($option->getKey(), $option->getValue());
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'field_type';
    }
}
