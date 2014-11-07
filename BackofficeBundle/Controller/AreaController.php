<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

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
        $area = $this->get('php_orchestra_model.repository.node')->findAreaFromNodeAndAreaId($node, $areaId);

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

        $this->handleForm(
            $form,
            $this->get('translator')->trans('php_orchestra_backoffice.form.area.success')
        );

        return $this->renderAdminForm($form);
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
                'action' => $this->generateUrl(
                    'php_orchestra_backoffice_template_area_form',
                    array(
                        'templateId' => $templateId,
                        'areaId' => $areaId
                    )
                ),
            )
        );

        $form->handleRequest($request);

        $this->handleForm(
            $form,
            $this->get('translator')->trans('php_orchestra_backoffice.form.area.success')
        );

        return $this->renderAdminForm($form);
    }
}
