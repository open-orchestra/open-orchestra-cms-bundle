<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\MediaFacade;
use PHPOrchestra\ModelBundle\Model\MediaInterface;

/**
 * Class MediaTransformer
 */
class MediaTransformer extends AbstractTransformer
{
    protected $mediathequeUrl;

    /**
     * @param string $mediathequeUrl
     */
    public function __construct($mediathequeUrl)
    {
        $this->mediathequeUrl = $mediathequeUrl;
    }

    /**
     * @param MediaInterface $mixed
     *
     * @return MediaFacade
     */
    public function transform($mixed)
    {
        $facade = new MediaFacade();

        $facade->id = $mixed->getId();
        $facade->name = $mixed->getName();
        $facade->mimeType = $mixed->getMimeType();
        $facade->displayedImage = $this->mediathequeUrl .'/' . $mixed->getThumbnail();

        $facade->addLink('_self_select', $mixed->getId());

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'media';
    }
}
