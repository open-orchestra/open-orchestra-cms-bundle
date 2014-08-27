<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use PHPOrchestra\ApiBundle\Facade\ContentTypeCollectionFacade;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

        $facade->addLink('_self_add', $this->getRouter()->generate(
            'php_orchestra_backoffice_content_type_new',
            array(),
            UrlGeneratorInterface::ABSOLUTE_URL
        ));

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
