<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;

/**
 * Class ContentCollectionTransformer
 */
class ContentCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param Collection  $contentCollection
     * @param string|null $contentType
     *
     * @return FacadeInterface
     */
    public function transform($contentCollection, $contentType = null)
    {
        $facade = $this->newFacade();

        foreach ($contentCollection as $content) {
            $facade->addContent($this->getTransformer('content')->transform($content));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content_collection';
    }
}
