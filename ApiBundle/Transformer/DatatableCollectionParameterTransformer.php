<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class DatatableCollectionParameterTransformer
 */
class DatatableCollectionParameterTransformer extends AbstractTransformer
{

    /**
     * @param array $dataTableCollectionParameter
     *
     * @return FacadeInterface
     */
    public function transform($dataTableCollectionParameter)
    {
        $facade = $this->newFacade();
        foreach ($dataTableCollectionParameter as $name => $dataTableEntityParameter) {
            if (!empty($dataTableEntityParameter)) {
                $facade->addEntityParameter($name, $this->getTransformer('datatable_entity_parameter')->transform($dataTableEntityParameter));
            }
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'datatable_collection_parameter';
    }
}
