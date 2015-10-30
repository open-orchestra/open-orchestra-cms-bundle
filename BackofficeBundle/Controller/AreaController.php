<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\GeneralNodesPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Event\TemplateEvent;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\TemplateInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\TemplateEvents;
use Symfony\Component\Form\FormInterface;
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
     * @Config\Route("/area/form/{nodeId}/{areaId}", name="open_orchestra_backoffice_area_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $nodeId, $areaId)
    {
        $node = $this->get('open_orchestra_model.repository.node')->find($nodeId);
        $area = $this->get('open_orchestra_model.repository.node')->findAreaByAreaId($node, $areaId);

        $actionUrl = $this->generateUrl('open_orchestra_backoffice_area_form', array(
            'nodeId' => $nodeId,
            'areaId' => $areaId
        ));

        $form = $this->generateForm($request, $actionUrl, $area);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.area.success');
        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(NodeEvents::NODE_UPDATE_AREA, new NodeEvent($node));
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     * @param string  $templateId
     * @param string  $areaId
     *
     * @config\Route("/template/area/form/{templateId}/{areaId}", name="open_orchestra_backoffice_template_area_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function templateFormAction(Request $request, $templateId, $areaId)
    {
        $template = $this->get('open_orchestra_model.repository.template')->findOneByTemplateId($templateId);
        $area = $this->get('open_orchestra_model.repository.template')->findAreaByAreaId($template, $areaId);
        $actionUrl = $this->generateUrl('open_orchestra_backoffice_template_area_form', array(
            'templateId' => $templateId,
            'areaId' => $areaId
        ));

        $form = $this->generateForm($request, $actionUrl, $area, $template);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.area.success');
        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(TemplateEvents::TEMPLATE_AREA_UPDATE, new TemplateEvent($template));
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request                $request
     * @param string                 $actionUrl
     * @param AreaInterface          $area
     * @param TemplateInterface|null $template
     *
     * @return FormInterface
     */
    protected function generateForm(Request $request, $actionUrl, $area, TemplateInterface $template = null)
    {
        $options = array('action' => $actionUrl);

        if ($template) {
            $options['disabled'] = !$this->get('open_orchestra_backoffice.authorize_edition.manager')->isEditable($template);
        }
        $form = parent::createForm('oo_area', $area, $options);

        $form->handleRequest($request);

        return $form;
    }
}
