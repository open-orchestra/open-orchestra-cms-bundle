<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\ApiBundle\Facade\ApiClientCollectionFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class ApiClientCollectionTransformer
 */
class ApiClientCollectionTransformer extends AbstractTransformer
{
    /**
     * @param Collection $apiClientCollection
     *
     * @return FacadeInterface
     */
    public function transform($apiClientCollection)
    {
        $facade = new ApiClientCollectionFacade();

        foreach ($apiClientCollection as $apiClient) {
            $facade->addApiClient($this->getTransformer('api_client')->transform($apiClient));
        }

        $facade->addLink('_self_add', $this->generateRoute('open_orchestra_backoffice_api_client_new'));

        return $facade;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'api_client_collection';
    }
}
