<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\ApiBundle\Facade\ContentAttributeFacade;
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

        $facade = new ContentAttributeFacade();

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
