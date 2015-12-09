<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class WidgetCollectionTransformer
 */
class WidgetCollectionTransformer extends AbstractTransformer
{
    /**
     * @param array $widgetCollection
     *
     * @return FacadeInterface
     */
    public function transform($widgetCollection)
    {
        $facade = $this->newFacade();

        foreach ($widgetCollection as $widget) {
            $facade->addWidget($this->getTransformer('widget')->transform($widget));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'widget_collection';
    }
}
