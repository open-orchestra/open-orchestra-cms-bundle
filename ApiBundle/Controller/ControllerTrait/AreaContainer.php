<?php

namespace OpenOrchestra\ApiBundle\Controller\ControllerTrait;

use OpenOrchestra\BaseApi\Transformer\TransformerInterface;
use OpenOrchestra\ModelInterface\Model\AreaContainerInterface;

/**
 * Trait AreaContainer
 */
trait AreaContainer
{
    /**
     * Update areas from an areaContainer
     *
     * @param array                  $areas
     * @param AreaContainerInterface $areaContainer
     * @param TransformerInterface   $transformerManager
     *
     *  @return AreaContainerInterface
     */
    protected function updateAreasFromContainer($areas, AreaContainerInterface $areaContainer, TransformerInterface $transformerManager)
    {
        $container = $this->get('open_orchestra_backoffice.manager.area')->updateAreasFromContainer($areas, $areaContainer);
        $this->get('document_manager')->flush();

        return $transformerManager->transform($container);
    }
}
