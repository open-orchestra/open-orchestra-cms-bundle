<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TreeController
 */
class TreeController extends Controller
{
    /**
     * List all nodes
     *
     * @return Response
     */
    public function showTreeNodesAction()
    {
        $nodes = $this->get('php_orchestra_model.repository.node')->findLastVersionBySiteId();

        return $this->render(
            'PHPOrchestraBackofficeBundle:Tree:showTreeNodes.html.twig',
            array(
                'nodes' => $nodes
            )
        );
    }

    /**
     * List all templates
     *
     * @return Response
     */
    public function showTreeTemplatesAction()
    {
        $templates = $this->get('php_orchestra_model.repository.template')->findByDeleted(false);

        return $this->render(
            'PHPOrchestraBackofficeBundle:Tree:showTreeTemplates.html.twig',
            array(
                'templates' => $templates
            )
        );
    }

    /**
     * List all contentType to sort the contents
     *
     * @return Response
     */
    public function showContentTypeForContentAction()
    {
        $contentTypes = $this->get('php_orchestra_model.repository.content_type')->findAll();

        return $this->render(
            'PHPOrchestraBackofficeBundle:Tree:showContentTypeForContent.html.twig',
            array(
                'contentTypes' => $contentTypes,
            )
        );
    }

    /**
     * List all root folders
     *
     * @return Response
     */
    public function showFolderTreeAction()
    {
//        $rootFolders = $this->get('php_orchestra_model.repository.media_folder')->findAllRootFolder();
        $rootFolders = $this->get('php_orchestra_model.repository.media_folder')->findAllRootFolderBySiteId();
        $contextManager = $this->get('php_orchestra_backoffice.context_manager');
        $siteRepository = $this->get('php_orchestra_model.repository.site');
        $site = $siteRepository->findOneBySiteId($contextManager->getCurrentSiteId());

        return $this->render( 'PHPOrchestraBackofficeBundle:Tree:showFolderTree.html.twig', array(
                'folders' => $rootFolders,
                'siteId' => $site->getId(),
        ));
    }
}
