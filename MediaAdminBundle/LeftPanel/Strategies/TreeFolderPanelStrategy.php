<?php

namespace OpenOrchestra\MediaAdminBundle\LeftPanel\Strategies;

use OpenOrchestra\Backoffice\LeftPanel\Strategies\AbstractLeftPanelStrategy;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
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
     * @var CurrentSiteIdInterface
     */
    protected $currentSiteManager;

    /**
     * @param FolderRepositoryInterface $folderRepository
     * @param CurrentSiteIdInterface    $currentSiteManager
     */
    public function __construct(FolderRepositoryInterface $folderRepository, CurrentSiteIdInterface $currentSiteManager)
    {
        $this->folderRepository = $folderRepository;
        $this->currentSiteManager = $currentSiteManager;
    }

    /**
     * @return string
     */
    public function show()
    {
        $siteId = $this->currentSiteManager->getCurrentSiteId();
        $rootFolders = $this->folderRepository->findAllRootFolderBySiteId($siteId);

        return $this->render( 'OpenOrchestraMediaAdminBundle:Tree:showFolderTree.html.twig', array(
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
