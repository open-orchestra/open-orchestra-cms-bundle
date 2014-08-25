<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\ContentCollectionFacade;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
    public function transform($mixed)
    {
        $facade = new ContentCollectionFacade();

        foreach ($mixed as $content) {
            $facade->addContent($this->getTransformer('content')->transform($content));
        }

        $facade->addLink('_self', $this->getRouter()->generate(
            'php_orchestra_api_content_list',
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
        return 'content_collection';
    }
}
