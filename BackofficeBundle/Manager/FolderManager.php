<?php

namespace PHPOrchestra\BackofficeBundle\Manager;

use PHPOrchestra\ModelBundle\Model\MediaFolderInterface;
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
     * @param DocumentManager  $documentManager
     */
    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * @param MediaFolderInterface $folder
     */
    public function deleteTree($folder)
    {
        if ($this->isDeletable($folder)) {
            $this->documentManager->remove($folder);
        }
    }

    /**
     * @param MediaFolderInterface $folder
     *
     * @return bool
     */
    public function isDeletable(MediaFolderInterface $folder)
    {
        return $this->countMediaTree($folder) == 0;
    }

    /**
     * @param MediaFolderInterface $folder
     *
     * @return int
     */
    protected function countMediaTree(MediaFolderInterface $folder)
    {
        $count = count($folder->getMedias());
        $subFolders = $folder->getSubFolders();
        foreach ($subFolders as $subFolder) {
            $count += $this->countMediaTree($subFolder, $count);
        }

        return $count;
    }
}
