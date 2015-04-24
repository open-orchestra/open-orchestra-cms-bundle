<?php

namespace OpenOrchestra\MediaAdminBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Transformer\AbstractTransformer;
use OpenOrchestra\MediaAdminBundle\Facade\MediaCollectionFacade;

/**
 * Class MediaCollectionTransformer
 */
class MediaCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     * @param string|null     $folderId
     * @param bool            $folderDeletable
     * @param string|null     $parentId
     *
     * @return FacadeInterface
     */
    public function transform($mixed, $folderId = null, $folderDeletable = false, $parentId = null)
    {
        $facade = new MediaCollectionFacade();

        $facade->isFolderDeletable = $folderDeletable;
        $facade->parentId = $parentId;

        foreach ($mixed as $media) {
            $facade->addMedia($this->getTransformer('media')->transform($media));
        }

        $facade->addLink('_self_add', $this->generateRoute('open_orchestra_backoffice_media_new', array(
            'folderId' => $folderId
        )));

        $facade->addLink('_self_folder', $this->generateRoute('open_orchestra_backoffice_folder_form', array(
            'folderId' => $folderId
        )));

        $facade->addLink('_self_delete', $this->generateRoute('open_orchestra_api_folder_delete', array(
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
