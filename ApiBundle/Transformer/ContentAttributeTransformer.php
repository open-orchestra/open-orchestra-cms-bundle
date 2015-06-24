<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeHttpException;
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
     * @throws TransformerParameterTypeHttpException
     */
    public function transform($contentAttribute)
    {
        if (!$contentAttribute instanceof ContentAttributeInterface) {
            throw new TransformerParameterTypeHttpException();
        }

        $facade = new ContentAttributeFacade();

        $facade->name = $contentAttribute->getName();
        $facade->value = $contentAttribute->getValue();
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
