<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ApiBundle\Facade\FieldTypeFacade;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;

/**
 * Class FieldTypeTransformer
 */
class FieldTypeTransformer extends AbstractTransformer
{
    protected $translationChoiceManager;

    /**
     * @param TranslationChoiceManager $translationChoiceManager
     */
    public function __construct(TranslationChoiceManager $translationChoiceManager)
    {
        $this->translationChoiceManager = $translationChoiceManager;
    }

    /**
     * @param FieldTypeInterface $fieldType
     *
     * @return \OpenOrchestra\BaseApi\Facade\FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($fieldType)
    {
        if (!$fieldType instanceof FieldTypeInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = new FieldTypeFacade();

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
