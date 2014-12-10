<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use PHPOrchestra\ApiBundle\Facade\Traits\BlameableFacade;
use PHPOrchestra\ApiBundle\Facade\Traits\TimestampableFacade;

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
