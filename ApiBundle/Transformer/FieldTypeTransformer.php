<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\Backoffice\Manager\MultiLanguagesChoiceManagerInterface;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;

/**
 * Class FieldTypeTransformer
 */
class FieldTypeTransformer extends AbstractTransformer
{
    protected $multiLanguagesChoiceManagerInterface;

    /**
     * @param string                               $facadeClass
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManagerInterface
     */
    public function __construct($facadeClass, MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManagerInterface)
    {
        parent::__construct($facadeClass);
        $this->multiLanguagesChoiceManagerInterface = $multiLanguagesChoiceManagerInterface;
    }

    /**
     * @param FieldTypeInterface $fieldType
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($fieldType)
    {
        if (!$fieldType instanceof FieldTypeInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->fieldId = $fieldType->getFieldId();
        $facade->label = $this->multiLanguagesChoiceManagerInterface->choose($fieldType->getLabels());
        $facade->defaultValue = $fieldType->getDefaultValue();
        $facade->searchable = $fieldType->isSearchable();
        $facade->listable = $fieldType->getListable();
        $facade->type = $fieldType->getType();

        foreach ($fieldType->getOptions() as $option) {
            $value = $option->getValue();
            if (!is_string($value))
                $value = \serialize($value);
            $facade->addOption($option->getKey(), $value);
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
