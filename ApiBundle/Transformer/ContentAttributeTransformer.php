<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Model\ContentAttributeInterface;

/**
 * Class ContentAttributeTransformer
 */
class ContentAttributeTransformer extends AbstractTransformer
{
    /**
     * @param ContentAttributeInterface $contentAttribute
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($contentAttribute)
    {
        if (!$contentAttribute instanceof ContentAttributeInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->name = $contentAttribute->getName();
        $facade->value = $contentAttribute->getValue();
        $facade->stringValue = $contentAttribute->getStringValue();

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content_attribute';
    }
}
