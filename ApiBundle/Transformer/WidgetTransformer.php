<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class WidgetTransformer
 */
class WidgetTransformer extends AbstractTransformer
{
    /**
     * @param string $widget
     *
     * @return FacadeInterface
     */
    public function transform($widget)
    {
        $facade = $this->newFacade();

        $facade->type = $widget;

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'widget';
    }
}
