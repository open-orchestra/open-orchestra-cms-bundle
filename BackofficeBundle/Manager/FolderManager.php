<?php

namespace PHPOrchestra\BackofficeBundle\Manager;

use PHPOrchestra\ModelBundle\Model\MediaFolderInterface;
use PHPOrchestra\ModelBundle\Repository\FolderRepository;
use PHPOrchestra\ModelBundle\Repository\MediaRepository;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Class FolderManager
 */
class FolderManager
{
    protected $folderRepository;
    protected $documentManager;

    /**
     * Constructor
     *
     * @param FolderRepository $folderRepository
     * @param DocumentManager  $documentManager
     */
    public function __construct(FolderRepository $folderRepository, DocumentManager $documentManager)
    {
        $this->folderRepository = $folderRepository;
        $this->documentManager = $documentManager;
    }

    /**
     * @param int  $folderId
     */
    public function deleteTree($folderId)
    {
        if($this->isDeletable($folderId)){
            $folder = $this->folderRepository->find($folderId);
            $this->documentManager->remove($folder);
        }
    }

    /**
     * @param int  $folderId
     */
    public function isDeletable($folderId)
    {
        $folder = $this->folderRepository->find($folderId);
        if($folder){
            return $this->countMediaTree($folder) == 0;
        }

        return true;
    }

    /**
     * @param FolderInterface  $folder
     */
    protected function countMediaTree(MediaFolderInterface $folder)
    {
        $count = count($folder->getMedias());
        $subFolders = $folder->getSubFolders();
        foreach($subFolders as $subFolder){
            $count += $this->countMediaTree($subFolder, $count);
        }

        return $count;
    }
}
