<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\ApiBundle\Facade\ContentTypeCollectionFacade;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;

/**
 * Class ContentTypeCollectionTransformer
 */
class ContentTypeCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param Collection $contentTypeCollection
     *
     * @return FacadeInterface
     */
    public function transform($contentTypeCollection)
    {
        $facade = new ContentTypeCollectionFacade();

        foreach ($contentTypeCollection as $contentType) {
            $facade->addContentType($this->getTransformer('content_type')->transform($contentType));
        }

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_CONTENT_TYPE)) {
            $facade->addLink('_self_add', $this->generateRoute(
                'open_orchestra_backoffice_content_type_new',
                array()
            ));
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
