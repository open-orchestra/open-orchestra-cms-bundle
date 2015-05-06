<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ApiBundle\Facade\ContentTypeCollectionFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

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

        $facade->addLink('_self_add', $this->generateRoute(
            'open_orchestra_backoffice_content_type_new',
            array()
        ));

        $facade->addLink('_translate', $this->generateRoute('open_orchestra_api_translate'));

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
