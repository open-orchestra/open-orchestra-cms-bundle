<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;

/**
 * Class AreaTransformer
 */
class AreaFlexTransformer extends AbstractTransformer
{
    /**
     * @param AreaFlexInterface $area
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     * @throws \OpenOrchestra\BaseApi\Exceptions\HttpException\FacadeClassNotSetException
     */
    public function transform($area)
    {
        if (!$area instanceof AreaFlexInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();
        $facade->label = $area->getLabel();
        $facade->areaId = $area->getAreaId();
        $facade->areaType = $area->getAreaType();
        foreach ($area->getAreas() as $subArea) {
            $facade->addArea($this->getTransformer('area_flex')->transform($subArea));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'area_flex';
    }
}
