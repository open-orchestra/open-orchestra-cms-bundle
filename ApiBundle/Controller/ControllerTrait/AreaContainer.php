<?php

namespace OpenOrchestra\ApiBundle\Controller\ControllerTrait;

use OpenOrchestra\BaseApi\Transformer\TransformerInterface;
use OpenOrchestra\ModelInterface\Model\AreaContainerInterface;
@trigger_error('The '.__NAMESPACE__.'\ChoiceArrayToStringTransformer class is deprecated since version 1.2.0 and will be removed in 2.0', E_USER_DEPRECATED);

/**
 * Trait AreaContainer
 * @deprecated will be removed in 2.0
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
     * @return AreaContainerInterface
     * @deprecated will be removed in 2.0
     */
    protected function updateAreasFromContainer($areas, AreaContainerInterface $areaContainer, TransformerInterface $transformerManager)
    {
        $container = $this->get('open_orchestra_backoffice.manager.area')->updateAreasFromContainer($areas, $areaContainer);
        $this->get('object_manager')->flush();

        return $transformerManager->transform($container);
    }
}
