<?php

namespace OpenOrchestra\ApiBundle\Controller\ControllerTrait;

use Doctrine\ODM\MongoDB\DocumentRepository;
use OpenOrchestra\BaseApi\Transformer\TransformerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trait HandleRequestDataTable
 */
trait HandleRequestDataTable
{
    /**
     * @param Request $request
     *
     * @return array
     */
    protected function extractParameterRequestDataTable(Request $request)
    {
        $columns = $request->get('columns');
        $search = $request->get('search');
        $search = (null !== $search && isset($search['value'])) ? $search['value'] : null;
        $order = $request->get('order');
        $skip = $request->get('start');
        $skip = (null !== $skip) ? (int)$skip : null;
        $limit = $request->get('length');
        $limit = (null !== $limit) ? (int)$limit : null;

        return array($columns, $search, $order, $skip, $limit);
    }

    /**
     * @param Request              $request
     * @param DocumentRepository   $entityRepository
     * @param array                $mappingEntity
     * @param TransformerInterface $transformerManager
     *
     * @return FacadeInterface
     */
    protected function handleRequestDataTable(Request $request, DocumentRepository $entityRepository, $mappingEntity, TransformerInterface $collectionTransformerManager, TransformerInterface $elementTransformerManager)
    {
        if ($entityId = $request->get('entityId')) {
            $element = $entityRepository->find($entityId);
            return $elementTransformerManager->transform($element);
        }

        list($columns, $search, $order, $skip, $limit) = $this->extractParameterRequestDataTable($request);

        $collection = $entityRepository->findForPaginateAndSearch($mappingEntity, $columns, $search, $order, $skip, $limit);
        $recordsTotal = $entityRepository->count();
        $recordsFiltered = $entityRepository->countWithSearchFilter($mappingEntity, $columns, $search);

        return $this->generateFacadeDataTable($collectionTransformerManager, $collection, $recordsTotal, $recordsFiltered);
    }

    /**
     * @param TransformerInterface $transformerManager
     * @param array                $collection
     * @param int                  $recordsTotal
     * @param int                  $recordsFiltered
     *
     * @return FacadeInterface
     */
    protected function generateFacadeDataTable(TransformerInterface $transformerManager, $collection, $recordsTotal, $recordsFiltered)
    {
        $facade = $transformerManager->transform($collection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
    }
}
