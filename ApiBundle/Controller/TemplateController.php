<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ModelInterface\Event\TemplateEvent;
use PHPOrchestra\ModelInterface\Model\TemplateInterface;
use PHPOrchestra\ModelInterface\TemplateEvents;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TemplateController
 *
 * @Config\Route("template")
 */
class TemplateController extends BaseController
{
    /**
     * @param string $templateId
     *
     * @Config\Route("/{templateId}", name="php_orchestra_api_template_show")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($templateId)
    {
        $template = $this->get('php_orchestra_model.repository.template')->findOneByTemplateId($templateId);

        return $this->get('php_orchestra_api.transformer_manager')->get('template')->transform($template);
    }

    /**
     * @param string $templateId
     *
     * @Config\Route("/{templateId}/delete", name="php_orchestra_api_template_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAction($templateId)
    {
        /** @var TemplateInterface $template */
        $template = $this->get('php_orchestra_model.repository.template')->findOneByTemplateId($templateId);
        $template->setDeleted(true);
        $this->dispatchEvent(TemplateEvents::TEMPLATE_DELETE, new TemplateEvent($template));
        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        return new Response('', 200);
    }
}
