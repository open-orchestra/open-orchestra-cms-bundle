<?php
namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class ApiClientCollectionFacade
 */
class ApiClientCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'api_clients';

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\ApiClientFacade>")
     */
    protected $apiClients = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addApiClient(FacadeInterface $facade)
    {
        $this->apiClients[] = $facade;
    }
}
