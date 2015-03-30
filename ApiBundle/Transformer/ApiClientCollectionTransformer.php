<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\ApiBundle\Facade\ApiClientCollectionFacade;
use OpenOrchestra\ApiBundle\Facade\FacadeInterface;

/**
 * Class ApiClientCollectionTransformer
 */
class ApiClientCollectionTransformer extends AbstractTransformer
{
    /**
     * @param Collection $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new ApiClientCollectionFacade();

        foreach ($mixed as $apiClient) {
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
