<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\Backoffice\Form\Type\AreaColumnType;
use OpenOrchestra\Backoffice\Form\Type\AreaRowType;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeTemplatePanelStrategy;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Event\TemplateEvent;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Model\AreaContainerInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\TemplateEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AreaController
 */
class AreaController extends AbstractEditionRoleController
{
    /**
     * @param Request $request
     * @param string  $templateId
     * @param string  $areaParentId
     *
     * @Config\Route("/area/template/row/new/{templateId}/{areaParentId}", name="open_orchestra_backoffice_template_new_row_area")
     * @Config\Method({"GET", "POST"})
     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_GENERAL_NODE')")
     *
     * @return Response
     */
    public function newRowFromTemplateAction(Request $request, $templateId, $areaParentId)
    {
        $template = $this->get('open_orchestra_model.repository.template')->findOneByTemplateId($templateId);
        $areaParent = $this->get('open_orchestra_model.repository.template')->findAreaInTemplateByAreaId($template, $areaParentId);
        $url =  $this->generateUrl('open_orchestra_backoffice_template_new_row_area', array(
            'templateId' => $templateId,
            'areaParentId' => $areaParentId,
        ));
        $form = $this->createNewRowForm($areaParent, $url);
        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.area.success');
        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(TemplateEvents::TEMPLATE_AREA_UPDATE, new TemplateEvent($template));
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     * @param string  $language
     * @param string  $version
     * @param string  $siteId
     * @param string  $areaParentId
     *
     * @Config\Route("/area/node/row/new/{siteId}/{nodeId}/{version}/{language}/{areaParentId}", name="open_orchestra_backoffice_node_new_row_area")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newRowFromNodeAction(Request $request, $nodeId, $language, $version, $siteId, $areaParentId)
    {
        $node = $this->get('open_orchestra_model.repository.node')->findVersion($nodeId, $language, $siteId, $version);
        $this->denyAccessUnlessGranted($this->getAccessRole($node), $node);
        $url = $this->generateUrl('open_orchestra_backoffice_node_new_row_area', array(
            'nodeId' => $nodeId,
            'language' => $language,
            'version' => $version,
            'siteId' => $siteId,
            'areaParentId' => $areaParentId,
        ));

        $areaParent = $this->get('open_orchestra_model.repository.node')->findAreaInNodeByAreaId($node, $areaParentId);
        $form = $this->createNewRowForm($areaParent, $url);
        $form->handleRequest($request);
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
     * @Config\Route("/area/template/row/{templateId}/{areaId}", name="open_orchestra_backoffice_area_form_row")
     * @Config\Method({"GET", "POST"})
     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_GENERAL_NODE')")
     *
     * @return Response
     */
    public function formTemplateAreaRowAction(Request $request, $templateId, $areaId)
    {
        $url = 'open_orchestra_backoffice_area_form_row';

        return $this->handleFormTemplateArea($request, $templateId, $areaId, $url, AreaRowType::class);
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     * @param string  $language
     * @param string  $version
     * @param string  $siteId
     * @param string  $areaId
     *
     * @Config\Route("/area/node/row/{siteId}/{nodeId}/{version}/{language}/{areaId}", name="open_orchestra_backoffice_node_area_form_row")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formNodeAreaRowAction(Request $request, $nodeId, $language, $version, $siteId, $areaId)
    {
        $url = 'open_orchestra_backoffice_node_area_form_row';

        return $this->handleFormNodeArea($request, $nodeId, $language, $version, $siteId, $areaId, $url, AreaRowType::class);
    }

    /**
     * @param Request $request
     * @param string  $templateId
     * @param string  $areaId
     *
     * @Config\Route("/area/template/column/{templateId}/{areaId}", name="open_orchestra_backoffice_area_form_column")
     * @Config\Method({"GET", "POST"})
     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_GENERAL_NODE')")
     *
     * @return Response
     */
    public function formTemplateAreaColumnAction(Request $request, $templateId, $areaId)
    {
        $url = 'open_orchestra_backoffice_area_form_column';

        return $this->handleFormTemplateArea($request, $templateId, $areaId, $url, AreaColumnType::class);
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     * @param string  $language
     * @param string  $version
     * @param string  $siteId
     * @param string  $areaId
     *
     * @Config\Route("/area/node/column/{siteId}/{nodeId}/{version}/{language}/{areaId}", name="open_orchestra_backoffice_node_area_form_column")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formNodeAreaColumnAction(Request $request, $nodeId, $language, $version, $siteId, $areaId)
    {
        $url = 'open_orchestra_backoffice_node_area_form_column';

        return $this->handleFormNodeArea($request, $nodeId, $language, $version, $siteId, $areaId, $url, AreaColumnType::class);
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
        $template = $this->get('open_orchestra_model.repository.template')->findOneByTemplateId($templateId);
        $area = $this->get('open_orchestra_model.repository.template')->findAreaInTemplateByAreaId($template, $areaId);

        $form = $this->createForm($formAreaType, $area, array(
            'action' => $this->generateUrl($url, array(
                'templateId' => $templateId,
                'areaId' => $areaId,
            ))
        ));
        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.area.success');
        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(TemplateEvents::TEMPLATE_AREA_UPDATE, new TemplateEvent($template));
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     * @param string  $language
     * @param string  $version
     * @param string  $siteId
     * @param string  $areaId
     * @param string  $url
     * @param string  $formAreaType
     *
     * @return Response
     */
    protected function handleFormNodeArea(Request $request, $nodeId, $language, $version, $siteId, $areaId, $url, $formAreaType)
    {
        $node = $this->get('open_orchestra_model.repository.node')->findVersion($nodeId, $language, $siteId, $version);
        $this->denyAccessUnlessGranted($this->getAccessRole($node), $node);

        $area = $this->get('open_orchestra_model.repository.node')->findAreaInNodeByAreaId($node, $areaId);

        $form = $this->createForm($formAreaType, $area, array(
            'action' => $this->generateUrl($url, array(
                'nodeId' => $nodeId,
                'language' => $language,
                'version' => $version,
                'siteId' => $siteId,
                'areaId' => $areaId,
            ))
        ));
        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.area.success');
        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(NodeEvents::NODE_UPDATE_AREA, new NodeEvent($node));
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param AreaInterface $areaParent
     * @param string        $url
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createNewRowForm(AreaInterface $areaParent, $url)
    {
        $areaManager = $this->get('open_orchestra_backoffice.manager.area');
        /** @var AreaInterface $areaRow */
        $areaRow = $areaManager->initializeNewAreaRow($areaParent);
        $areaParent->addArea($areaRow);

        $form = $this->createForm(AreaRowType::class, $areaRow, array(
            'action' => $url
        ));

        return $form;
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     * @param string  $areaId
     *
     * @Config\Route("/area/form/{nodeId}/{areaId}", name="open_orchestra_backoffice_area_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     * @deprecated will be removed in 2.0
     */
    public function formAction(Request $request, $nodeId, $areaId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        $node = $this->get('open_orchestra_model.repository.node')->find($nodeId);
        $this->denyAccessUnlessGranted($this->getAccessRole($node), $node);
        $area = $this->get('open_orchestra_model.repository.node')->findAreaByAreaId($node, $areaId);

        $actionUrl = $this->generateUrl('open_orchestra_backoffice_area_form', array(
            'nodeId' => $nodeId,
            'areaId' => $areaId
        ));

        $form = $this->generateForm($request, $actionUrl, $area, $node, $this->getEditionRole($node));
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
     * @deprecated will be removed in 2.0
     *
     * @return Response
     */
    public function templateFormAction(Request $request, $templateId, $areaId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        $template = $this->get('open_orchestra_model.repository.template')->findOneByTemplateId($templateId);
        $area = $this->get('open_orchestra_model.repository.template')->findAreaByAreaId($template, $areaId);
        $actionUrl = $this->generateUrl('open_orchestra_backoffice_template_area_form', array(
            'templateId' => $templateId,
            'areaId' => $areaId
        ));

        $form = $this->generateForm($request, $actionUrl, $area, $template, TreeTemplatePanelStrategy::ROLE_ACCESS_UPDATE_TEMPLATE);
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
     * @param AreaContainerInterface $areaContainer
     * @param string                 $role
     *
     * @return FormInterface
     *
     * @deprecated will be removed in 2.0
     */
    protected function generateForm(Request $request, $actionUrl, $area, AreaContainerInterface $areaContainer, $role)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        $options = array('action' => $actionUrl);

        $options['disabled'] = !$this->get('security.authorization_checker')->isGranted($role, $areaContainer);
        $form = parent::createForm('oo_area', $area, $options);

        $form->handleRequest($request);

        return $form;
    }
}
