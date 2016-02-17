<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\Backoffice\Form\Type\AreaFlexColumnType;
use OpenOrchestra\ModelInterface\Event\TemplateFlexEvent;
use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;
use OpenOrchestra\ModelInterface\TemplateFlexEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\Backoffice\Form\Type\AreaFlexRowType;

/**
 * Class AreaFlexController
 */
class AreaFlexController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $templateId
     * @param string  $areaParentId
     *
     * @Config\Route("/area_flex/row/new/{templateId}/{areaParentId}", name="open_orchestra_backoffice_new_row_area_flex")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newRowFromTemplateAction(Request $request, $templateId, $areaParentId)
    {
        $template = $this->get('open_orchestra_model.repository.template_flex')->findOneByTemplateId($templateId);
        $areaParent = $this->get('open_orchestra_model.repository.template_flex')->findAreaInTemplateByAreaId($template, $areaParentId);

        $areaManager = $this->get('open_orchestra_backoffice.manager.area_flex');
        /** @var AreaFlexInterface $areaRow */
        $areaRow = $areaManager->initializeNewAreaRow($areaParent);
        $areaParent->addArea($areaRow);

        $form = $this->createForm(AreaFlexRowType::class, $areaRow, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_new_row_area_flex', array(
                'templateId' => $templateId,
                'areaParentId' => $areaParentId,
            ))
        ));
        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.area_flex.success');
        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(TemplateFlexEvents::TEMPLATE_FLEX_AREA_UPDATE, new TemplateFlexEvent($template));
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     * @param string  $templateId
     * @param string  $areaId
     *
     * @Config\Route("/area_flex/row/{templateId}/{areaId}", name="open_orchestra_backoffice_area_flex_form_row")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formTemplateAreaRowAction(Request $request, $templateId, $areaId)
    {
        $url = 'open_orchestra_backoffice_area_flex_form_row';

        return $this->handleFormTemplateArea($request, $templateId, $areaId, $url, AreaFlexRowType::class);
    }

    /**
     * @param Request $request
     * @param string  $templateId
     * @param string  $areaId
     *
     * @Config\Route("/area_flex/column/{templateId}/{areaId}", name="open_orchestra_backoffice_area_flex_form_column")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formTemplateAreaColumnAction(Request $request, $templateId, $areaId)
    {
        $url = 'open_orchestra_backoffice_area_flex_form_column';

        return $this->handleFormTemplateArea($request, $templateId, $areaId, $url, AreaFlexColumnType::class);
    }

    /**
     * @param Request $request
     * @param string  $templateId
     * @param string  $areaId
     * @param string  $url
     * @param string $formAreaType
     *
     * @return Response
     */
    protected function handleFormTemplateArea(Request $request, $templateId, $areaId, $url, $formAreaType)
    {
        $template = $this->get('open_orchestra_model.repository.template_flex')->findOneByTemplateId($templateId);
        $area = $this->get('open_orchestra_model.repository.template_flex')->findAreaInTemplateByAreaId($template, $areaId);

        $form = $this->createForm($formAreaType, $area, array(
            'action' => $this->generateUrl($url, array(
                'templateId' => $templateId,
                'areaId' => $areaId,
            ))
        ));
        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.area_flex.success');
        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(TemplateFlexEvents::TEMPLATE_FLEX_AREA_UPDATE, new TemplateFlexEvent($template));
        }

        return $this->renderAdminForm($form);
    }
}
