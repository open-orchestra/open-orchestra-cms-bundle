<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
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
     * @param FieldTypeInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new FieldTypeFacade();

        $facade->fieldId = $mixed->getFieldId();
        $facade->label = $this->translationChoiceManager->choose($mixed->getLabels());
        $facade->defaultValue = $mixed->getDefaultValue();
        $facade->searchable = $mixed->getSearchable();
        $facade->type = $mixed->getType();

        foreach ($mixed->getOptions() as $option) {
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
