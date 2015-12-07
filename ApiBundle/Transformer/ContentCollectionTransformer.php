<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\ApiBundle\Facade\ContentCollectionFacade;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\ContentTypeForContentPanelStrategy;
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
        $facade = new ContentCollectionFacade();

        foreach ($contentCollection as $content) {
            if ($this->authorizationChecker->isGranted(ContentTypeForContentPanelStrategy::ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT, $content)) {
                $facade->addContent($this->getTransformer('content')->transform($content));
            }
        }

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_content_list',
            array()
        ));

        if ($contentType) {
            $facade->addLink('_self_add', $this->generateRoute(
                'open_orchestra_backoffice_content_new',
                array('contentType' => $contentType)
            ));
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
