<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Facade\ContentCollectionFacade;

/**
 * Class ContentCollectionTransformer
 */
class ContentCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed, $contentType = null)
    {
        $facade = new ContentCollectionFacade();

        foreach ($mixed as $content) {
            $facade->addContent($this->getTransformer('content')->transform($content));
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
