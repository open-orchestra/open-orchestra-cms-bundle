<?php

namespace OpenOrchestra\MediaAdminBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;
use OpenOrchestra\BaseApi\Facade\Traits\BlameableFacade;
use OpenOrchestra\BaseApi\Facade\Traits\TimestampableFacade;

/**
 * Class MediaFacade
 */
class MediaFacade extends AbstractFacade
{
    use BlameableFacade;
    use TimestampableFacade;

    /**
     * @Serializer\Type("string")
     */
    public $name;

    /**
     * @Serializer\Type("string")
     */
    public $publicLink;

    /**
     * @Serializer\Type("string")
     */
    public $mimeType;

    /**
     * @Serializer\Type("string")
     */
    public $displayedImage;

    /**
     * @Serializer\Type("boolean")
     */
    public $isDeletable;

    /**
     * @Serializer\Type("string")
     */
    public $alt;

    /**
     * @Serializer\Type("string")
     */
    public $title;

    /**
     * @Serializer\Type("array<string, string>")
     */
    protected $thumbnails = array();

    /**
     * @param string $key
     * @param string $link
     */
    public function addThumbnail($key, $link)
    {
        $this->thumbnails[$key] = $link;
    }
}
