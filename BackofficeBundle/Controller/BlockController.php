<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class BlockController
 */
class BlockController extends Controller
{
    /**
     * Render the node Block form
     *
     * @param string $type
     *
     * @Config\Route("/admin/block/form/{type}", name="php_orchestra_backoffice_block_form")
     *
     * @return JsonResponse|Response
     */
    public function formAction($type)
    {
        $refresh = array('is_node' => ($type == 'node'));
        $form = $this->createForm(
            'blocks',
            $refresh,
            array(
                'action' => $this->generateUrl('php_orchestra_backoffice_block_form', array('type' => $type)),
                'inDialog' => true,
                'subForm' => true,
                'beginJs' => array('pagegenerator/'.$type.'_block.js?'.time(), 'pagegenerator/dialogNode.js'),
            )
        );

        return $this->render(
            'PHPOrchestraCMSBundle:Form:form.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }
}
