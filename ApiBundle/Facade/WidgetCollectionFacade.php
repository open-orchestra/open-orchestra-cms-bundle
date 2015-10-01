<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class WidgetCollectionFacade
 */
class WidgetCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'widgets';

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\WidgetFacade>")
     */
    protected $widgets = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addWidget(FacadeInterface $facade)
    {
        $this->widgets[] = $facade;
    }
}
