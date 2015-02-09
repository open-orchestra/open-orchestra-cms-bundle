<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use PHPOrchestra\ModelInterface\Event\NodeEvent;
use PHPOrchestra\ModelInterface\Event\TemplateEvent;
use PHPOrchestra\ModelInterface\Model\AreaInterface;
use PHPOrchestra\ModelInterface\NodeEvents;
use PHPOrchestra\ModelInterface\TemplateEvents;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AreaController
 */
class AreaController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $nodeId
     * @param string  $areaId
     *
     * @Config\Route("/area/form/{nodeId}/{areaId}", name="php_orchestra_backoffice_area_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $nodeId, $areaId)
    {
        $node = $this->get('php_orchestra_model.repository.node')->find($nodeId);
        $area = $this->get('php_orchestra_model.repository.node')->findAreaByAreaId($node, $areaId);

        $actionUrl = $this->generateUrl('php_orchestra_backoffice_area_form', array(
            'nodeId' => $nodeId,
            'areaId' => $areaId
        ));

        $this->dispatchEvent(NodeEvents::NODE_UPDATE_AREA, new NodeEvent($node));

        return $this->handleAreaForm($request, $actionUrl, $area);
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
        $template = $this->get('php_orchestra_model.repository.template')->findOneByTemplateId($templateId);
        $area = $this->get('php_orchestra_model.repository.template')->findAreaByAreaId($template, $areaId);
        $actionUrl = $this->generateUrl('php_orchestra_backoffice_template_area_form', array(
            'templateId' => $templateId,
            'areaId' => $areaId
        ));

        $this->dispatchEvent(TemplateEvents::TEMPLATE_AREA_UPDATE, new TemplateEvent($template));

        return $this->handleAreaForm($request, $actionUrl, $area);
    }

    /**
     * @param Request       $request
     * @param string        $actionUrl
     * @param AreaInterface $area
     *
     * @return Response
     */
    protected function handleAreaForm(Request $request, $actionUrl, $area)
    {
        $form = $this->createForm('area', $area, array(
            'action' => $actionUrl,
        ));

        $form->handleRequest($request);

        $this->handleForm(
            $form,
            $this->get('translator')->trans('php_orchestra_backoffice.form.area.success')
        );

        return $this->renderAdminForm($form);
    }
}
