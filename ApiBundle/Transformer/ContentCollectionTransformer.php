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
            $facade->addContent($this->getContext()->transform('content', $content));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param null            $source
     *
     * @return array
     */
    public function reverseTransform(FacadeInterface $facade, $source = null)
    {
        $contents = array();
        $contentsFacade = $facade->getContents();
        foreach ($contentsFacade as $contentFacade) {
            $content = $this->getContext()->reverseTransform('content', $contentFacade);
            if (null !== $content) {
                $contents[] = $content;
            }
        }

        return $contents;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content_collection';
    }
}
