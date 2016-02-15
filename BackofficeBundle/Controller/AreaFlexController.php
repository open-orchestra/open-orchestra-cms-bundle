<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\ModelInterface\Event\TemplateFlexEvent;
use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;
use OpenOrchestra\ModelInterface\TemplateFlexEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BackofficeBundle\Form\Type\AreaFlexType;

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
        $areaRow = $areaManager->initializeNewAreaRow();
        $areaParent->addArea($areaRow);

        $form = $this->createForm(AreaFlexType::class, $areaRow, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_new_row_area_flex', array(
                'templateId' => $templateId,
                'areaParentId' => $areaParentId,
            ))
        ));
        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.area.success');
        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(TemplateFlexEvents::TEMPLATE_FLEX_AREA_UPDATE, new TemplateFlexEvent($template));
        }

        return $this->renderAdminForm($form);
    }
}
