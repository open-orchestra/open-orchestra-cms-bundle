<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\TemplateEvent;
use OpenOrchestra\ModelInterface\Model\TemplateInterface;
use OpenOrchestra\ModelInterface\TemplateEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\ApiBundle\Controller\ControllerTrait\AreaContainer;

/**
 * Class TemplateController
 *
 * @Config\Route("template")
 *
 * @Api\Serialize()
 */
class TemplateController extends BaseController
{
    use AreaContainer;

    /**
     * @param string $templateId
     *
     * @Config\Route("/{templateId}", name="open_orchestra_api_template_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_TREE_TEMPLATE')")
     *
     * @return FacadeInterface
     */
    public function showAction($templateId)
    {
        $template = $this->get('open_orchestra_model.repository.template')->findOneByTemplateId($templateId);

        return $this->get('open_orchestra_api.transformer_manager')->get('template')->transform($template);
    }

    /**
     * @param string $templateId
     *
     * @Config\Route("/gs/{templateId}", name="open_orchestra_api_gs_template_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_TREE_TEMPLATE')")
     *
     * @return FacadeInterface
     */
    public function showGSAction($templateId)
    {
        $template = $this->get('open_orchestra_model.repository.template')->findOneByTemplateId($templateId);

        return $this->get('open_orchestra_api.transformer_manager')->get('template')->transform($template);
    }

    /**
     * @param string|null $templateId
     *
     * @Config\Route("/update-area-in-template/{templateId}", name="open_orchestra_api_areas_update_in_template")
     * @Config\Method({"POST"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_TREE_NODE')")
     *
     * @return Response
     */
    public function updateAreasInTemplateAction(Request $request, $templateId)
    {
        $areaContainer = $this->get('open_orchestra_model.repository.template')->findOneByTemplateId($templateId);
        $this->dispatchEvent(TemplateEvents::TEMPLATE_AREA_UPDATE, new TemplateEvent($areaContainer));
        return $this->updateAreasFromContainer($request->get('areas'), $areaContainer, $this->get('open_orchestra_api.transformer_manager')->get('template'));
    }

    /**
     * @param string $templateId
     *
     * @Config\Route("/{templateId}/delete", name="open_orchestra_api_template_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_TREE_TEMPLATE')")
     *
     * @return Response
     */
    public function deleteAction($templateId)
    {
        /** @var TemplateInterface $template */
        $template = $this->get('open_orchestra_model.repository.template')->findOneByTemplateId($templateId);
        $template->setDeleted(true);
        $this->dispatchEvent(TemplateEvents::TEMPLATE_DELETE, new TemplateEvent($template));
        $this->get('object_manager')->flush();

        return array();
    }
}
