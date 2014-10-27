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

    /**
     * Constructor
     *
     * @param FolderRepository $folderRepository
     * @param DocumentManager  $documentManager
     */
    public function __construct(FolderRepository $folderRepository, MediaRepository $mediaRepository, DocumentManager $documentManager)
    {
        $this->folderRepository = $folderRepository;
        $this->mediaRepository = $mediaRepository;
        $this->documentManager = $documentManager;
    }

    /**
     * @param FolderInterface  $folder
     */
    public function deleteTree(MediaFolderInterface $folder)
    {
        if($this->countMediaTree($folder) == 0){
            $this->documentManager->remove($folder);
        }
    }

    public function countMediaTree(MediaFolderInterface $folder, $count = 0)
    {
        $count += count($folder->getMedias());
        $subFolders = $folder->getSubFolders();
        foreach($subFolders as $subFolder){
            $count = $this->countMediaTree($subFolder, $count);
        }

        return $count;
    }
}
