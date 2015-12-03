<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\WidgetCollectionFacade;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\ContentTypeForContentPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;

/**
 * Class WidgetCollectionTransformer
 */
class WidgetCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{

    /**
     * @param array $widgetCollection
     *
     * @return FacadeInterface
     */
    public function transform($widgetCollection)
    {
        $facade = new WidgetCollectionFacade();

        foreach ($widgetCollection as $widget) {
            $role = $this->getWidgetRole($widget);
            if ($this->authorizationChecker->isGranted($role)) {
                $facade->addWidget($this->getTransformer('widget')->transform($widget));
            }
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

    /**
     * @param $widget
     *
     * @return null|string
     */
    protected function getWidgetRole($widget)
    {
        $role = null;

        if (preg_match('/node/', $widget)) {
            $role = TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE;
        } elseif (preg_match('/content/', $widget)) {
            $role = ContentTypeForContentPanelStrategy::ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT;
        }

        return $role;
    }
}
