<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;

/**
 * Class FieldTypeTransformer
 */
class FieldTypeTransformer extends AbstractTransformer
{
    protected $translationChoiceManager;

    /**
     * @param string                   $facadeClass
     * @param TranslationChoiceManager $translationChoiceManager
     */
    public function __construct($facadeClass, TranslationChoiceManager $translationChoiceManager)
    {
        parent::__construct($facadeClass);
        $this->translationChoiceManager = $translationChoiceManager;
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
        $facade->label = $this->translationChoiceManager->choose($fieldType->getLabels());
        $facade->defaultValue = $fieldType->getDefaultValue();
        $facade->searchable = $fieldType->getSearchable();
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
