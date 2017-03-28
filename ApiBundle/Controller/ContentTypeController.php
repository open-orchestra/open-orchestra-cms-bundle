<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\ContentTypeEvents;
use OpenOrchestra\ModelInterface\Event\ContentTypeEvent;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

/**
 * Class ContentTypeController
 *
 * @Config\Route("content-type")
 *
 * @Api\Serialize()
 */
class ContentTypeController extends BaseController
{
    /**
     * @param string $contentTypeId
     *
     * @return FacadeInterface
     *
     * @Config\Route("/{contentTypeId}", name="open_orchestra_api_content_type_show")
     * @Config\Method({"GET"})
     *
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::FIELD_TYPES
     * })
     */
    public function showAction($contentTypeId)
    {
        $contentType = $this->get('open_orchestra_model.repository.content_type')->findOneByContentTypeIdInLastVersion($contentTypeId);

        return $this->get('open_orchestra_api.transformer_manager')->get('content_type')->transform($contentType);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_content_type_list")
     * @Config\Method({"GET"})
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::AUTHORIZATIONS
     * })
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, ContentTypeInterface::ENTITY_TYPE);
        $mapping = array(
            'name' => 'names',
            'content_type_id' => 'contentTypeId',
            'linked_to_site' => 'linkedToSite'
        );
        $configuration = PaginateFinderConfiguration::generateFromRequest($request, $mapping);
        $repository = $this->get('open_orchestra_model.repository.content_type');

        $collection = $repository->findAllNotDeletedInLastVersionForPaginate($configuration);
        $recordsTotal = $repository->countByContentTypeInLastVersion();
        $recordsFiltered = $repository->countNotDeletedInLastVersionWithSearchFilter($configuration);
        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('content_type_collection');
        $facade = $collectionTransformer->transform($collection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/delete-multiple", name="open_orchestra_api_content_type_delete_multiple")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteContentTypesAction(Request $request)
    {
        $format = $request->get('_format', 'json');

        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            $this->getParameter('open_orchestra_api.facade.content_type_collection.class'),
            $format
        );
        $contentTypeRepository = $this->get('open_orchestra_model.repository.content_type');
        $contentTypes = $this->get('open_orchestra_api.transformer_manager')->get('content_type_collection')->reverseTransform($facade);
        $contentTypeIds = array();
        foreach ($contentTypes as $contentType) {
            if ($this->isGranted(ContributionActionInterface::DELETE, $contentType) &&
                $this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(ContributionActionInterface::DELETE, $contentType)
            ) {
                $contentTypeIds[] = $contentType->getContentTypeId();
                $this->dispatchEvent(ContentTypeEvents::CONTENT_TYPE_DELETE, new ContentTypeEvent($contentType));
            }
        }
        $contentTypeRepository->removeByContentTypeId($contentTypeIds);

        return array();
    }

    /**
     * @Config\Route("/content/content-type-list", name="open_orchestra_api_content_type_list_for_content")
     * @Config\Method({"GET"})
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::AUTHORIZATIONS
     * })
     * @return FacadeInterface
     */
    public function listForContentAction()
    {
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $site = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($siteId);

        $repository = $this->get('open_orchestra_model.repository.content_type');
        $collection = $repository->findAllNotDeletedInLastVersion($site->getContentTypes());

        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('content_type_collection');
        $facade = $collectionTransformer->transform($collection);

        return $facade;
    }

    /**
     * @param string $contentTypeId
     *
     * @Config\Route("/{contentTypeId}/delete", name="open_orchestra_api_content_type_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     * @throws ContentTypeNotAllowedException
     */
    public function deleteAction($contentTypeId)
    {
        if (0 == $this->get('open_orchestra_model.repository.content')->countByContentType($contentTypeId)) {
            $contentTypes = $this->get('open_orchestra_model.repository.content_type')->findBy(array('contentTypeId' => $contentTypeId));
            if (count($contentTypes) > 0) {
                foreach ($contentTypes as $contentType) {
                    $this->denyAccessUnlessGranted(ContributionActionInterface::DELETE, $contentType);
                }
                if (!$this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(ContributionActionInterface::DELETE, $contentTypes[0])) {
                    $this->createAccessDeniedException();
                }

                $this->get('open_orchestra_backoffice.manager.content_type')->delete($contentTypes);
                $this->dispatchEvent(ContentTypeEvents::CONTENT_TYPE_DELETE, new ContentTypeEvent($contentTypes[0]));
                $this->get('object_manager')->flush();
            }
        }

        return array();
    }
}
