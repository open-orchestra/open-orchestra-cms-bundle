<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class StatusCollectionFacade
 */
class StatusCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'statuses';

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\StatusFacade>")
     */
    protected $statuses = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addStatus(FacadeInterface $facade)
    {
        $this->statuses[] = $facade;
    }
}
