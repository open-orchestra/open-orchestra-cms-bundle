<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use PHPOrchestra\BackofficeBundle\Form\Type\AreaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AreaController
 */
class AreaController extends Controller
{
    /**
     * @param Request $request
     * @param string  $nodeId
     * @param string  $areaId
     *
     * @config\Route("/area/form/{nodeId}/{areaId}", name="php_orchestra_backoffice_area_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $nodeId, $areaId)
    {
        $node = $this->get('php_orchestra_model.repository.node')->findOneByNodeId($nodeId);
        $area = $this->get('php_orchestra_model.repository.node')->findAreaByNodeIdAndAreaId($nodeId, $areaId);

        $form = $this->createForm(
            'area',
            $area,
            array(
                'action' => $this->generateUrl('php_orchestra_backoffice_area_form', array(
                    'nodeId' => $nodeId,
                    'areaId' => $areaId
                )),
                'node' => $node
            )
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('php_orchestra_backoffice.form.area.success')
            );
        }

        return $this->render(
            'PHPOrchestraBackofficeBundle:Editorial:template.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    /**
     * @param Request $request
     * @param string  $templateId
     * @param string  $areaId
     *
     * @config\Route("/template/area/form/{templateId}/{areaId}", name="php_orchestra_backoffice_template_area_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function templateFormAction(Request $request, $templateId, $areaId)
    {
        $area = $this->get('php_orchestra_model.repository.template')->findAreaByTemplateIdAndAreaId($templateId, $areaId);

        $form = $this->createForm(
            'template_area',
            $area,
            array(
                'action' => $this->generateUrl('php_orchestra_backoffice_template_area_form', array(
                        'templateId' => $templateId,
                        'areaId' => $areaId
                    )),
            )
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('php_orchestra_backoffice.form.area.success')
            );
        }

        return $this->render(
            'PHPOrchestraBackofficeBundle:Editorial:template.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     * @param int     $areaId
     * @param int     $blockPosition
     *
     * @Config\Route("/block/form/{nodeId}/{areaId}/{blockPosition}", name="php_orchestra_backoffice_area_remove_block", requirements={"blockPosition" = "\d+"}, defaults={"blockPosition" = 0})
     * @Config\Method({"POST", "DELETE"})
     *
     * @return Response
     */
    public function removeAction(Request $request, $nodeId, $areaId, $blockPosition = 0)
    {
        $node = $this->get('php_orchestra_model.repository.node')->findOneByNodeId($nodeId);
        $areas = $node->getAreas();
        
        foreach ($areas as $area) {
            if ($area->getAreaId() == $areaId) {
                $blocks = $area->getBlocks();
                unset($blocks[$blockPosition]);
                $area->setBlocks($blocks);
                break;
            }
        }
        
        $node->setAreas($areas);
        
        $this->get('doctrine.odm.mongodb.document_manager')->flush();
exit();
       /* return $this->render('PHPOrchestraBackofficeBundle:Editorial:template.html.twig', array(
            'form' => $form->createView(),
            'blockType' => $block->getComponent()
        ));*/
    }
}
