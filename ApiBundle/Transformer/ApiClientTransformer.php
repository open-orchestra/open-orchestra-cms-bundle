<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\BaseApi\Model\ApiClientInterface;

/**
 * Class ApiClientTransformer
 */
class ApiClientTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param ApiClientInterface $apiClient
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($apiClient)
    {
        if (!$apiClient instanceof ApiClientInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();
        $facade->id = $apiClient->getId();
        $facade->name = $apiClient->getName();
        $facade->trusted = $apiClient->isTrusted();
        $facade->key = $apiClient->getKey();
        $facade->secret = $apiClient->getSecret();
        $facade->roles = implode(',', $apiClient->getRoles());

        return $facade;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'api_client';
    }
}
