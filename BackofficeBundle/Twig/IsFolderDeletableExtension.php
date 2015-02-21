<?php

namespace OpenOrchestra\BackofficeBundle\Twig;

use OpenOrchestra\BackofficeBundle\Manager\FolderManager;
use OpenOrchestra\Media\Model\MediaFolderInterface;

/**
 * Class IsFolderDeletableExtension
 */
class IsFolderDeletableExtension extends \Twig_Extension
{
    protected $folderManager;

    /**
     * @param FolderManager $folderManager
     */
    public function __construct(FolderManager $folderManager)
    {
        $this->folderManager = $folderManager;
    }

    /**
     * @param MediaFolderInterface $folder
     *
     * @return boolean
     */
    public function isFolderDeletable(MediaFolderInterface $folder)
    {
        return $this->folderManager->isDeletable($folder);
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('is_folder_deletable', array($this, 'isFolderDeletable')),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'is_folder_deletable';
    }
}
