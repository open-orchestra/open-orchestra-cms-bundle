<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\MediaFacade;
use PHPOrchestra\Media\Model\MediaInterface;

/**
 * Class MediaTransformer
 */
class MediaTransformer extends AbstractTransformer
{
    protected $mediathequeUrl;
    protected $thumbnailConfig;

    /**
     * @param string $mediathequeUrl
     * @param array  $thumbnailConfig
     */
    public function __construct($mediathequeUrl, array $thumbnailConfig)
    {
        $this->mediathequeUrl = $mediathequeUrl;
        $this->thumbnailConfig = $thumbnailConfig;
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

        foreach ($this->thumbnailConfig as $key => $thumbnail) {
            // TODO use the url generator for the images with is not done yet
            $facade->addThumbnail($key, $this->mediathequeUrl . '/' . $key . '-' . $mixed->getFilesystemName());
        }

        $facade->addLink('_self_select', $mixed->getId());
        $facade->addLink('_self_crop', $this->generateRoute('php_orchestra_backoffice_media_crop', array(
            'mediaId' => $mixed->getId()
        )));
        $facade->addLink('_self_meta', $this->generateRoute('php_orchestra_backoffice_media_meta', array(
            'mediaId' => $mixed->getId()
        )));
        $facade->addLink('_self_delete', $this->generateRoute('php_orchestra_api_media_delete', array(
            'mediaId' => $mixed->getId()
        )));

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
