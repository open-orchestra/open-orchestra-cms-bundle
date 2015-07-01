<?php

namespace OpenOrchestra\ApiBundle\Controller\ControllerTrait;

use OpenOrchestra\BaseApi\Transformer\TransformerInterface;
use OpenOrchestra\ModelInterface\Repository\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\ModelInterface\Repository\PaginateRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trait HandleRequestDataTable
 */
trait HandleRequestDataTable
{
    /**
     * @param Request $request
     *
     * @return PaginateFinderConfiguration
     */
    protected function extractParameterRequestDataTable(Request $request)
    {
        $configuration = new PaginateFinderConfiguration();
        $configuration->setColumns($request->get('columns'));
        $search= $request->get('search');
        if (isset($search['value'])){
            $configuration->setSearch($search['value']);
        }
        $configuration->setOrder($request->get('order'));
        $configuration->setSkip($request->get('start'));
        $configuration->setLimit($request->get('length'));

        return $configuration;
    }

    /**
     * @param Request                     $request
     * @param PaginateRepositoryInterface $entityRepository
     * @param array                       $mappingEntity
     * @param TransformerInterface        $transformerManager
     *
     * @return FacadeInterface
     */
    protected function handleRequestDataTable(Request $request, PaginateRepositoryInterface $entityRepository, $mappingEntity, TransformerInterface $transformerManager)
    {
        if ($entityId = $request->get('entityId')) {
            $element = $entityRepository->find($entityId);
            return $transformerManager->transform(array($element));
        }

        $configuration = $this->extractParameterRequestDataTable($request);
        $configuration->setDescriptionEntity($mappingEntity);
        $collection = $entityRepository->findForPaginate($configuration);
        $recordsTotal = $entityRepository->count();
        $recordsFiltered = $entityRepository->countWithFilter($configuration->getFinderConfiguration());

        return $this->generateFacadeDataTable($transformerManager, $collection, $recordsTotal, $recordsFiltered);
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
