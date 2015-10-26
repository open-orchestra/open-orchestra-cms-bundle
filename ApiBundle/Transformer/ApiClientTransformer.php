<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\ApiBundle\Facade\ApiClientFacade;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\BaseApi\Model\ApiClientInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class ApiClientTransformer
 */
class ApiClientTransformer extends AbstractTransformer
{
    protected $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

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

        $facade = new ApiClientFacade();
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
