<?php

namespace OpenOrchestra\LogBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * Class LogRepository
 */
class LogRepository extends DocumentRepository implements LogRepositoryInterface
{
    /**
     * @param array|null  $descriptionEntity
     * @param array|null  $columns
     * @param string|null $search
     * @param array|null  $order
     * @param int|null    $skip
     * @param int|null    $limit
     *
     * @return array
     */
    public function findForPaginateAndSearch($descriptionEntity = null, $columns = null, $search = null, $order = null, $skip = null, $limit = null)
    {
        $qb = $this->createQueryWithSearchAndOrderFilter($descriptionEntity, $columns, $search, $order);

        if (null !== $skip && $skip > 0) {
            $qb->skip($skip);
        }

        if (null !== $limit) {
            $qb->limit($limit);
        }

        return $qb->getQuery()->execute();
    }

    /**
     * @return int
     */
    public function count()
    {
        $qb = $this->createQueryBuilder();

        return $qb->count()->getQuery()->execute();
    }

    /**
     * @param array|null   $columns
     * @param array|null   $descriptionEntity
     * @param array|null   $search
     *
     * @return int
     */
    public function countFilterSearch($descriptionEntity = null, $columns = null, $search = null)
    {
        $qb = $this->createQueryWithSearchFilter($descriptionEntity, $columns, $search);

        return $qb->count()->getQuery()->execute();
    }

    /**
     * @param string $value
     * @param string $type
     *
     * @return mixed
     */
    protected function getFilterSearchField($value, $type)
    {
        if ($type == 'integer') {
            $filter = (int) $value;
        } elseif ($type == 'boolean') {
            $value = ($value === 'true' || $value === '1') ? true : false;
            $filter = $value;
        } else {
            $filter = new \MongoRegex('/.*'.$value.'.*/i');
        }

        return $filter;
    }

    /**
     * @param array|null  $descriptionEntity
     * @param array|null  $columns
     * @param string|null $search
     *
     * @return \Doctrine\ODM\MongoDB\Query\Builder
     */
    protected function createQueryWithSearchFilter($descriptionEntity = null, $columns = null, $search = null)
    {
        $qb = $this->createQueryBuilder();
        if (null !== $columns) {
            foreach ($columns as $column) {
                $columnsName = $column['name'];
                if (isset($descriptionEntity[$columnsName]) && isset($descriptionEntity[$columnsName]['key'])) {
                    $descriptionAttribute = $descriptionEntity[$columnsName];
                    $name = $descriptionAttribute['key'];
                    $type = isset($descriptionAttribute['type']) ? $descriptionAttribute['type'] : null;
                    if ($column['searchable'] && !empty($column['search']['value']) && !empty($name)) {
                        $value = $column['search']['value'];
                        $qb->addAnd($qb->expr()->field($name)->equals($this->getFilterSearchField($value, $type)));
                    }
                    if (!empty($search) && $column['searchable'] && !empty($name)) {
                        $qb->addOr($qb->expr()->field($name)->equals($this->getFilterSearchField($search, $type)));
                    }
                }
            }
        }

        return $qb;
    }

    /**
     * @param array|null  $descriptionEntity
     * @param array|null  $columns
     * @param string|null $search
     * @param array|null  $order
     *
     * @return \Doctrine\ODM\MongoDB\Query\Builder
     */
    protected function createQueryWithSearchAndOrderFilter($descriptionEntity = null, $columns = null, $search = null, $order = null)
    {
        $qb = $this->createQueryWithSearchFilter($descriptionEntity, $columns, $search);

        if (null !== $order && null !== $columns) {
            foreach ($order as $orderColumn) {
                $numberColumns = $orderColumn['column'];
                if ($columns[$numberColumns]['orderable']) {
                    if (!empty($columns[$numberColumns]['name'])) {
                        $columnsName = $columns[$numberColumns]['name'];
                        if (isset($descriptionEntity[$columnsName]) && isset($descriptionEntity[$columnsName]['key'])) {
                            $name = $descriptionEntity[$columnsName]['key'];
                            $dir = ($orderColumn['dir'] == 'desc') ? -1 : 1;
                            $qb->sort($name, $dir);
                        }
                    }
                }
            }
        }

        return $qb;
    }

}
