<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\WidgetCollectionFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class WidgetCollectionTransformer
 */
class WidgetCollectionTransformer extends AbstractTransformer
{
    /**
     * @param array $contentCollection
     *
     * @return FacadeInterface
     */
    public function transform($widgetCollection)
    {
        $facade = new WidgetCollectionFacade();

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
