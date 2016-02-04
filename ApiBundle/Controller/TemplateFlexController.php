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
 * Class TemplateFlexController
 *
 * @Config\Route("template_flex")
 *
 * @Api\Serialize()
 */
class TemplateFlexController extends BaseController
{
    use AreaContainer;

    /**
     * @param string $templateId
     *
     * @Config\Route("/{templateId}", name="open_orchestra_api_template_flex_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_TREE_TEMPLATE')")
     *
     * @return FacadeInterface
     */
    public function showAction($templateId)
    {
        $template = $this->get('open_orchestra_model.repository.template_flex')->findOneByTemplateId($templateId);

        return $this->get('open_orchestra_api.transformer_manager')->get('template_flex')->transform($template);
    }
}
