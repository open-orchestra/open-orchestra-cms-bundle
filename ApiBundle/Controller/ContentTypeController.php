<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\HandleRequestDataTable;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\ContentTypeEvents;
use OpenOrchestra\ModelInterface\Event\ContentTypeEvent;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
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
        $repository = $this->get('open_orchestra_model.repository.content_type');
        $transformer = $this->get('open_orchestra_api.transformer_manager')->get('content_type_collection');

        if ($request->get('entityId')) {
            $element = $repository->find($request->get('entityId'));
            return $transformer->transform(array($element));
        }

        $configuration = PaginateFinderConfiguration::generateFromRequest($request);
        $configuration->setDescriptionEntity(array(
            'content_type_id' => array('key' => 'contentTypeId'),
            'name'            => array('key' => 'name'),
            'version'         => array('key' => 'version' , 'type' => 'integer'),
        ));
        $contentTypeCollection = $repository->findAllNotDeletedInLastVersionForPaginate($configuration);
        $recordsTotal = $repository->countByContentTypeInLastVersion();
        $recordsFiltered = $repository->countNotDeletedInLastVersionWithSearchFilter($configuration);

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
        $this->get('object_manager')->flush();

        return new Response('', 200);
    }
}
