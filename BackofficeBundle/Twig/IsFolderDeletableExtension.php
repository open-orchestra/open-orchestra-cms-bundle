<?php

namespace PHPOrchestra\BackofficeBundle\Twig;

use Doctrine\Common\Collections\Collection;
use PHPOrchestra\BackofficeBundle\Manager\FolderManager;
use PHPOrchestra\Media\Model\MediaFolderInterface;

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
