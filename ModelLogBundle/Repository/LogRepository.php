<?php

namespace OpenOrchestra\ModelLogBundle\Repository;

use OpenOrchestra\LogBundle\Repository\LogRepositoryInterface;
use OpenOrchestra\Repository\AbstractAggregateRepository;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Solution\MongoAggregation\Pipeline\Stage;

/**
 * Class LogRepository
 */
class LogRepository extends AbstractAggregateRepository implements LogRepositoryInterface
{
    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return array
     */
    public function findForPaginate(PaginateFinderConfiguration $configuration)
    {
        $qa = $this->createAggregationQuery();

        $this->filterSearch($configuration, $qa);

        $order = $configuration->getOrder();
        if (!empty($order)) {
            $qa->sort($order);
        }

        $qa->skip($configuration->getSkip());
        $qa->limit($configuration->getLimit());

        return $this->hydrateAggregateQuery($qa);
    }

    /**
     * @return int
     */
    public function count()
    {
        $qa = $this->createAggregationQuery();

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     *
     * @return int
     */
    public function countWithFilter(PaginateFinderConfiguration $configuration)
    {
        $qa = $this->createAggregationQuery();
        $this->filterSearch($configuration, $qa);

        return $this->countDocumentAggregateQuery($qa);
    }

    /**
     * @param PaginateFinderConfiguration $configuration
     * @param Stage                       $qa
     *
     * @return array
     */
    protected function filterSearch(PaginateFinderConfiguration $configuration, Stage $qa)
    {
        $qa->match(array('extra.site_id' => $configuration->getSearchIndex('site_id')));

        $userName = $configuration->getSearchIndex('user_name');
        if (null !== $userName && '' !== $userName) {
            $qa->match(array('extra.user_name' => new \MongoRegex('/.*' . $userName . '.*/i')));
        }

        $userIP = $configuration->getSearchIndex('user_ip');
        if (null !== $userIP && '' !== $userIP) {
            $qa->match(array('extra.user_ip' => new \MongoRegex('/.*' . $userIP . '.*/i')));
        }

        $date = $configuration->getSearchIndex('date');
        if (null !== $date && '' !== $date) {
            $dateFormat = $configuration->getSearchIndex('date-format');
            if (null !== $dateFormat && '' !== $dateFormat) {
                $dateFormat = str_replace('yy', 'Y', $dateFormat);
                $dateFormat = str_replace('mm', 'm', $dateFormat);
                $dateFormat = str_replace('dd', 'd', $dateFormat);
                $date = \DateTime::createFromFormat($dateFormat, $date)->format('Y-m-d');
                $qa->match(array('datetime' => new \MongoRegex('/' . $date . '.*/i')));
            }
        }

        return $qa;
    }
}
