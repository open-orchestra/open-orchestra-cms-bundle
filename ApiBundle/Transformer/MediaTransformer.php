<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\MediaFacade;
use PHPOrchestra\Media\Model\MediaInterface;

/**
 * Class MediaTransformer
 */
class MediaTransformer extends AbstractTransformer
{
    protected $thumbnailConfig;

    /**
     * @param array  $thumbnailConfig
     */
    public function __construct(array $thumbnailConfig)
    {
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
        $facade->isDeletable = $mixed->isDeletable();
        $facade->displayedImage = $this->generateRoute('php_orchestra_media_get', array(
            'key' => $mixed->getThumbnail()
        ));

        foreach ($this->thumbnailConfig as $format => $thumbnail) {
            $facade->addThumbnail(
                $format,
                $this->generateRoute('php_orchestra_media_get', array(
                    'key' => $format . '-' . $mixed->getFilesystemName()
                ))
            );
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
