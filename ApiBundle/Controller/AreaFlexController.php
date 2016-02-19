<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ModelInterface\Event\TemplateFlexEvent;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\ModelInterface\TemplateFlexEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

/**
 * Class AreaFlexController
 *
 * @Config\Route("area-flex")
 *
 * @Api\Serialize
 */
class AreaFlexController extends BaseController
{
    /**
     * @param string      $areaId
     * @param string      $templateId
     * @param string|null $areaParentId
     *
     * @Config\Route("/{areaId}/delete-column-in-template/{templateId}/{areaParentId}", name="open_orchestra_api_area_flex_column_delete_in_template")
     * @Config\Route("/{areaId}/delete-row-in-template/{templateId}", name="open_orchestra_api_area_flex_row_delete_in_template")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_TEMPLATE_FLEX')")
     *
     * @return Response
     */
    public function deleteAreaInTemplateAction($areaId, $templateId, $areaParentId = null)
    {
        $template = $this->get('open_orchestra_model.repository.template_flex')->findOneByTemplateId($templateId);
        $rootArea = $template->getArea();
        $removedArea = $areaId;
        if (null !== $areaParentId) {
            $parentArea = $this->get('open_orchestra_model.repository.template_flex')->findAreaInTemplateByAreaId($template, $areaParentId);
            if (1 === count($parentArea->getAreas())) {
                $removedArea = $parentArea->getAreaId();
            }
        }
        if (null !== $rootArea) {
            $rootArea->removeAreaByAreaId($removedArea);
            $this->dispatchEvent(TemplateFlexEvents::TEMPLATE_FLEX_AREA_DELETE, new TemplateFlexEvent($template));
            $this->get('object_manager')->flush();
        }

        return array();
    }

    /**
     * @param Request $request
     * @param string  $areaParentId
     * @param string  $templateId
     *
     * @Config\Route("/{areaParentId}/{templateId}/move_area", name="open_orchestra_api_area_flex_move_in_template")
     * @Config\Method({"POST"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_TREE_TEMPLATE_FLEX')")
     *
     * @return Response
     */
    public function moveAreaInTemplateAction(Request $request, $areaParentId, $templateId)
    {
        $areaFacade = $this->get('jms_serializer')->deserialize($request->getContent(), 'OpenOrchestra\ApiBundle\Facade\AreaFlexFacade', $request->get('_format', 'json'));

        $template = $this->get('open_orchestra_model.repository.template_flex')->findOneByTemplateId($templateId);
        $areaParent = $this->get('open_orchestra_model.repository.template_flex')->findAreaInTemplateByAreaId($template, $areaParentId);

        $this->get('open_orchestra_api.transformer.area_flex')->reverseTransform($areaFacade, $areaParent);
        $this->get('object_manager')->flush();

        return array();
    }
}
