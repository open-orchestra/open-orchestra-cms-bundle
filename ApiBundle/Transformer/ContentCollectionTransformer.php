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
     * @param Collection $contentCollection
     * @param array      $params
     *
     * @return FacadeInterface
     */
    public function transform($contentCollection, array $params = array())
    {
        $facade = $this->newFacade();

        foreach ($contentCollection as $content) {
            $facade->addContent($this->getContext()->transform('content', $content));
        }

        return $facade;
    }

    /**
     * @param FacadeInterface $facade
     * @param array           $params
     *
     * @return array
     */
    public function reverseTransform(FacadeInterface $facade, array $params = array())
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
