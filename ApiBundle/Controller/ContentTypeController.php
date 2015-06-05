<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\HandleRequestDataTable;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\ContentTypeEvents;
use OpenOrchestra\ModelInterface\Event\ContentTypeEvent;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

/**
 * Class ContentTypeController
 *
 * @Config\Route("content-type")
 */
class ContentTypeController extends BaseController
{
    use HandleRequestDataTable;

    /**
     * @param string $contentTypeId
     *
     * @Config\Route("/{contentTypeId}", name="open_orchestra_api_content_type_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_CONTENT_TYPE')")
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
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_content_type_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_CONTENT_TYPE')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        list($columns, $search, $order, $skip, $limit) = $this->extractParameterRequestDataTable($request);

        $columnsNameToEntityAttribute = array(
            'content_type_id' => array('key' => 'contentTypeId'),
            'name'            => array('key' => 'name'),
            'version'         => array('key' => 'version' , 'type' => 'integer'),
        );

        $repository = $this->get('open_orchestra_model.repository.content_type');
        $contentTypeCollection = $repository->findAllByDeletedInLastVersionForPaginateAndSearch($columnsNameToEntityAttribute, $columns, $search, $order, $skip, $limit);
        $recordsTotal = $repository->countByContentTypeInLastVersion();
        $recordsFiltered = $repository->countByDeletedInLastVersionWithSearchFilter($columnsNameToEntityAttribute, $columns, $search);

        $transformer = $this->get('open_orchestra_api.transformer_manager')->get('content_type_collection');

        return $this->generateFacadeDataTable($transformer, $contentTypeCollection, $recordsTotal, $recordsFiltered);
    }

    /**
     * @param string $contentTypeId
     *
     * @Config\Route("/{contentTypeId}/delete", name="open_orchestra_api_content_type_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_CONTENT_TYPE')")
     *
     * @return Response
     */
    public function deleteAction($contentTypeId)
    {
        $contentTypes = $this->get('open_orchestra_model.repository.content_type')->findBy(array('contentTypeId' => $contentTypeId));
        $this->get('open_orchestra_backoffice.manager.content_type')->delete($contentTypes);
        $this->dispatchEvent(ContentTypeEvents::CONTENT_TYPE_DELETE, new ContentTypeEvent(current($contentTypes)));
        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        return new Response('', 200);
    }
}
