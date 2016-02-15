<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\TemplateFlexEvent;
use OpenOrchestra\ModelInterface\Model\TemplateFlexInterface;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\ModelInterface\TemplateFlexEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
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
        /** @var TemplateFlexInterface $template */
        $template = $this->get('open_orchestra_model.repository.template_flex')->findOneByTemplateId($templateId);
        $template->setDeleted(true);
        $this->dispatchEvent(TemplateFlexEvents::TEMPLATE_FLEX_DELETE, new TemplateFlexEvent($template));
        $this->get('object_manager')->flush();

        return array();
    }
}
