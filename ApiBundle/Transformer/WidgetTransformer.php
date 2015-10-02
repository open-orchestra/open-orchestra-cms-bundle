<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\WidgetFacade;
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
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($widget)
    {
        $facade = new WidgetFacade();

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
