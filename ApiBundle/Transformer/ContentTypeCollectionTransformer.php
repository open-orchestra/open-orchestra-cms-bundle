<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

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
            $facade->addContentType($this->getTransformer('content_type')->transform($contentType));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content_type_collection';
    }
}
