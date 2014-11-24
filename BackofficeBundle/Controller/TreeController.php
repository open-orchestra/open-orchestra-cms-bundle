<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use PHPOrchestra\ModelBundle\Model\NodeInterface;
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
     * List all general nodes
     *
     * @return Response
     */
    public function showGeneralTreeNodesAction()
    {
        $nodes = $this->get('php_orchestra_model.repository.node')->findLastVersionBySiteId(NodeInterface::TYPE_GENERAL);

        return $this->render(
            'PHPOrchestraBackofficeBundle:Tree:showGeneralTreeNodes.html.twig',
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
        $contentTypes = $this->get('php_orchestra_model.repository.content_type')->findAllByDeletedInLastVersion();

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
        $rootFolders = $this->get('php_orchestra_model.repository.media_folder')->findAllRootFolderBySiteId();

        return $this->render( 'PHPOrchestraBackofficeBundle:Tree:showFolderTree.html.twig', array(
                'folders' => $rootFolders,
        ));
    }
}
