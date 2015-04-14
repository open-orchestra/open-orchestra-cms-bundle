<?php

namespace OpenOrchestra\Backoffice\LeftPanel\Strategies;

use OpenOrchestra\Media\Repository\FolderRepositoryInterface;

/**
 * Class TreeFolderPanel
 */
class TreeFolderPanelStrategy extends AbstractLeftPanelStrategy
{
    const ROLE_ACCESS_TREE_FOLDER = 'ROLE_ACCESS_TREE_FOLDER';

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

        return $this->render( 'OpenOrchestraBackofficeBundle:Tree:showFolderTree.html.twig', array(
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

    /**
     * @return string
     */
    public function getRole()
    {
        return self::ROLE_ACCESS_TREE_FOLDER;
    }
}
