<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class DatatableColumnParameterTransformer
 */
class DatatableColumnParameterTransformer extends AbstractTransformer
{
    /**
     * @param array $dataTableColumnParameter
     *
     * @return FacadeInterface
     */
    public function transform($dataTableColumnParameter)
    {
        $facade = $this->newFacade();
        $facade->name = $dataTableColumnParameter["name"];
        $facade->title = $dataTableColumnParameter["title"];
        $facade->visible = $dataTableColumnParameter["visible"];
        if (isset($dataTableColumnParameter["searchField"])) {
            $facade->searchField = $dataTableColumnParameter["searchField"];
        }
        if (isset($dataTableColumnParameter["activateColvis"])) {
            $facade->activateColvis = $dataTableColumnParameter["activateColvis"];
        }
        if (isset($dataTableColumnParameter["searchable"])) {
            $facade->searchable = $dataTableColumnParameter["searchable"];
        }
        if (isset($dataTableColumnParameter["orderable"])) {
            $facade->orderable = $dataTableColumnParameter["orderable"];
        }
        if (isset($dataTableColumnParameter["orderDirection"])) {
            $facade->orderDirection = $dataTableColumnParameter["orderDirection"];
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'datatable_column_parameter';
    }
}
