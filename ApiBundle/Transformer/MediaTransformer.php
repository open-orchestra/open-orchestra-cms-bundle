<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\MediaFacade;
use PHPOrchestra\ModelBundle\Model\MediaInterface;

/**
 * Class MediaTransformer
 */
class MediaTransformer extends AbstractTransformer
{
    /**
     * @param MediaInterface $mixed
     *
     * @return MediaFacade
     */
    public function transform($mixed)
    {
        $facade = new MediaFacade();

        $facade->name = $mixed->getName();

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
