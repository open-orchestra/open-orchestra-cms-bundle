<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\ContentTypeEvents;
use OpenOrchestra\ModelInterface\Event\ContentTypeEvent;
use OpenOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContentTypeController
 *
 * @Config\Route("content-type")
 */
class ContentTypeController extends BaseController
{
    /**
     * @param string $contentTypeId
     *
     * @Config\Route("/{contentTypeId}", name="open_orchestra_api_content_type_show")
     * @Config\Method({"GET"})
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($contentTypeId)
    {
        $contentType = $this->get('open_orchestra_model.repository.content_type')->findOneByContentTypeId($contentTypeId);

        return $this->get('open_orchestra_api.transformer_manager')->get('content_type')->transform($contentType);
    }

    /**
     * @Config\Route("", name="open_orchestra_api_content_type_list")
     * @Config\Method({"GET"})
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $contentTypeCollection = $this->get('open_orchestra_model.repository.content_type')->findAllByDeletedInLastVersion();

        return $this->get('open_orchestra_api.transformer_manager')->get('content_type_collection')->transform($contentTypeCollection);
    }

    /**
     * @param string $contentTypeId
     *
     * @Config\Route("/{contentTypeId}/delete", name="open_orchestra_api_content_type_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAction($contentTypeId)
    {
        $contentType = $this->get('open_orchestra_model.repository.content_type')->findOneBy(array('contentTypeId' => $contentTypeId));
        $contentType->setDeleted(true);
        $this->dispatchEvent(ContentTypeEvents::CONTENT_TYPE_DELETE, new ContentTypeEvent($contentType));
        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        return new Response('', 200);
    }
}
