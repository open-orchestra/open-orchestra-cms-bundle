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
     * @param string|null $templateId
     *
     * @Config\Route("/{areaId}/delete-in-template/{templateId}", name="open_orchestra_api_area_flex_delete_in_template")
     * @Config\Method({"POST", "DELETE", "GET"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_TEMPLATE_FLEX')")
     *
     * @return Response
     */
    public function deleteAreaInTemplateAction($areaId, $templateId)
    {
        $template = $this->get('open_orchestra_model.repository.template_flex')->findOneByTemplateId($templateId);
        $rootArea = $template->getArea();
        if (null !== $rootArea) {
            $rootArea->removeAreaByAreaId($areaId);
            $this->dispatchEvent(TemplateFlexEvents::TEMPLATE_FLEX_AREA_DELETE, new TemplateFlexEvent($template));
            $this->get('object_manager')->flush();
        }

        return array();
    }
}
