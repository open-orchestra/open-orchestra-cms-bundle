<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use PHPOrchestra\ApiBundle\Facade\ContentTypeCollectionFacade;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;

/**
 * Class ContentTypeCollectionTransformer
 */
class ContentTypeCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new ContentTypeCollectionFacade();

        foreach ($mixed as $contentType) {
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
