<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Cache\ArrayCache;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;

/**
 * Class FieldTypeTransformer
 */
class FieldTypeTransformer extends AbstractTransformer
{
    protected $multiLanguagesChoiceManagerInterface;

    /**
     * @param ArrayCache                           $arrayCache
     * @param string                               $facadeClass
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManagerInterface
     * @param array                                $fieldsParameters
     */
    public function __construct(
        ArrayCache $arrayCache,
        $facadeClass,
        MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManagerInterface,
        array $fieldsParameters
    ) {
        parent::__construct($arrayCache, $facadeClass);
        $this->multiLanguagesChoiceManagerInterface = $multiLanguagesChoiceManagerInterface;
        $this->fieldsParameters = $fieldsParameters;
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
        $facade->search = array_key_exists($fieldType->getType(), $this->fieldsParameters) && isset($this->fieldsParameters[$fieldType->getType()]['search']) ? $this->fieldsParameters[$fieldType->getType()]['search'] : '';
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
