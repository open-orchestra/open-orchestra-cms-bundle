<?php

namespace OpenOrchestra\ApiBundle\Facade;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

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
