<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;

/**
 * Class ContentTypeCollectionTransformer
 */
class ContentTypeCollectionTransformer extends AbstractTransformer
{
    /**
     * @param Collection $contentTypeCollection
     *
     * @return FacadeInterface
     */
    public function transform($contentTypeCollection)
    {
        $facade = $this->newFacade();

        foreach ($contentTypeCollection as $contentType) {
            $facade->addContentType($this->getContext()->transform('content_type', $contentType));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param null $source
     *
     * @return ContentTypeInterface|null
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        $contentTypes = array();
        $contentTypesFacade = $facade->getContentTypes();
        foreach ($contentTypesFacade as $contentTypeFacade) {
            $contentType = $this->getContext()->reverseTransform('content_type', $contentTypeFacade);
            if (null !== $contentType) {
                $contentTypes[] = $contentType;
            }
        }

        return $contentTypes;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'content_type_collection';
    }
}
