<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
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

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_API_CLIENT)) {
            $facade->addLink(
                '_self_delete',
                $this->generateRoute(
                    'open_orchestra_api_api_client_delete',
                    array('apiClientId' => $apiClient->getId())
                )
            );
        }

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_API_CLIENT)) {
            $facade->addLink(
                '_self_form',
                $this->generateRoute(
                    'open_orchestra_backoffice_api_client_form',
                    array('apiClientId' => $apiClient->getId())
                )
            );
        }

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
