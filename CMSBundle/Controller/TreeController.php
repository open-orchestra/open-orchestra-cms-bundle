<?php

namespace PHPOrchestra\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use PHPOrchestra\CMSBundle\Helper\TreeHelper;

/**
 * Class TreeController
 */
class TreeController extends Controller
{
    /**
     * List all nodes
     */
    public function showTreeNodesAction()
    {
        $nodes = $this->get('php_orchestra_model.repository.node')->findByDeleted(false);

        return $this->render(
            'PHPOrchestraCMSBundle:Tree:showTreeNodes.html.twig',
            array(
                'nodes' => $nodes
            )
        );
    }

    /**
     * List all templates
     */
    public function showTreeTemplatesAction()
    {
        $templates = $this->get('php_orchestra_model.repository.template')->findByDeleted(false);

        return $this->render(
            'PHPOrchestraCMSBundle:Tree:showTreeTemplates.html.twig',
            array(
                'templates' => $templates
            )
        );
    }
}
