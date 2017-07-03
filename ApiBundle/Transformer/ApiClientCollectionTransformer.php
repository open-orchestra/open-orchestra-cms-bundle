<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;

/**
 * Class ApiClientCollectionTransformer
 */
class ApiClientCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param Collection $apiClientCollection
     * @param array|null $params
     *
     * @return FacadeInterface
     */
    public function transform($apiClientCollection, array $params = null)
    {
        $facade = $this->newFacade();

        foreach ($apiClientCollection as $apiClient) {
            $facade->addApiClient($this->getContext()->transform('api_client', $apiClient));
        }

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
