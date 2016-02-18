<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ModelInterface\Event\TemplateFlexEvent;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\ModelInterface\TemplateFlexEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
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
}
