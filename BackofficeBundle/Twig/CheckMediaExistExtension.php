<?php

namespace PHPOrchestra\BackofficeBundle\Twig;

use Doctrine\Common\Collections\Collection;
use PHPOrchestra\BackofficeBundle\Manager\FolderManager;
use PHPOrchestra\ModelBundle\Repository\FolderRepository;

/**
 * Class CheckMediaExistExtension
 */
class CheckMediaExistExtension extends \Twig_Extension
{
    protected $folderManager;
    protected $folderRepository;

    /**
     * @param FolderManager $folderManager
     */
    public function __construct(FolderManager $folderManager, FolderRepository $folderRepository)
    {
        $this->folderManager = $folderManager;
        $this->folderRepository = $folderRepository;
    }

    /**
     * @param string $folderId
     *
     * @return boolean
     */
    public function checkMediaExist($folderId)
    {
        $folder = $this->folderRepository->find($folderId);
        if ($folder) {

            return $this->folderManager->countMediaTree($folder) != 0;
        }else {

            return true;
        }
        return true;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('check_media_exist', array($this, 'checkMediaExist')),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'check_media_exist';
    }
}
