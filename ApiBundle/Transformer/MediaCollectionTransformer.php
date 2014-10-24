<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\MediaCollectionFacade;

/**
 * Class MediaCollectionTransformer
 */
class MediaCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     * @param string|null     $folderId
     *
     * @return FacadeInterface
     */
    public function transform($mixed, $folderId = null)
    {
        $facade = new MediaCollectionFacade();

        foreach ($mixed as $media) {
            $facade->addMedia($this->getTransformer('media')->transform($media));
        }

        $facade->addLink('_self_add', $this->generateRoute('php_orchestra_backoffice_media_new', array(
            'folderId' => $folderId
        )));

        $facade->addLink('_self_folder', $this->generateRoute('php_orchestra_backoffice_folder_form', array(
            'folderId' => $folderId
        )));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'media_collection';
    }
}
