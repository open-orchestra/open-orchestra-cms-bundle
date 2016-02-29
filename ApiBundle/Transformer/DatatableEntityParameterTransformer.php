<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class DatatableEntityParameterTransformer
 */
class DatatableEntityParameterTransformer extends AbstractTransformer
{
    /**
     * @param array $dataTableEntityParameter
     *
     * @return FacadeInterface
     */
    public function transform($dataTableEntityParameter)
    {
        $facade = $this->newFacade();
        foreach ($dataTableEntityParameter as $dataTableColumnParameter) {
            $facade->addColumnParameter($this->getTransformer('datatable_column_parameter')->transform($dataTableColumnParameter));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'datatable_entity_parameter';
    }
}
