<?php

namespace OpenOrchestra\ApiBundle\Controller\ControllerTrait;

use OpenOrchestra\BaseApi\Transformer\TransformerInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\Pagination\Configuration\PaginationRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trait HandleRequestDataTable
 */
trait HandleRequestDataTable
{
    /**
     * @param Request                       $request
     * @param PaginationRepositoryInterface $entityRepository
     * @param array                         $mappingEntity
     * @param TransformerInterface          $transformerManager
     * @param array                         $filter
     *
     * @return \OpenOrchestra\BaseApi\Facade\FacadeInterface
     */
    protected function handleRequestDataTable(Request $request, PaginationRepositoryInterface $entityRepository, $mappingEntity, TransformerInterface $transformerManager, array $filter = array())
    {
        if ($entityId = $request->get('entityId')) {
            $element = $entityRepository->find($entityId);

            return $transformerManager->transform(array($element));
        }
        $configuration = PaginateFinderConfiguration::generateFromRequest($request);
        foreach ($filter as $key => $value) {
            $configuration->addPrefilterSearch($key, $value);
        }
        $configuration->setDescriptionEntity($mappingEntity);
        $collection = $entityRepository->findForPaginate($configuration);
        $recordsTotal = $entityRepository->count($configuration);
        $recordsFiltered = $entityRepository->countWithFilter($configuration);

        return $this->generateFacadeDataTable($transformerManager, $collection, $recordsTotal, $recordsFiltered);
    }

    /**
     * @param TransformerInterface $transformerManager
     * @param array                $collection
     * @param int                  $recordsTotal
     * @param int                  $recordsFiltered
     *
     * @return \OpenOrchestra\BaseApi\Facade\FacadeInterface
     */
    protected function generateFacadeDataTable(TransformerInterface $transformerManager, $collection, $recordsTotal, $recordsFiltered)
    {
        $facade = $transformerManager->transform($collection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
    }
}
