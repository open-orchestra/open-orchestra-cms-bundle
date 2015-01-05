<?php

namespace PHPOrchestra\Backoffice\LeftPanel\Strategies;

use PHPOrchestra\Media\Repository\FolderRepositoryInterface;

/**
 * Class TreeFolderPanel
 */
class TreeFolderPanelStrategy extends AbstractLeftPaneStrategy
{
    /**
     * @var FolderRepositoryInterface
     */
    protected $folderRepository;

    /**
     * @param FolderRepositoryInterface $folderRepository
     */
    public function __construct(FolderRepositoryInterface $folderRepository)
    {
        $this->folderRepository = $folderRepository;
    }

    /**
     * @return string
     */
    public function show()
    {
        $rootFolders = $this->folderRepository->findAllRootFolderBySiteId();

        return $this->render( 'PHPOrchestraBackofficeBundle:Tree:showFolderTree.html.twig', array(
            'folders' => $rootFolders,
        ));
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return self::EDITORIAL;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'folders';
    }
}
