<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\ApiBundle\Facade\ApiClientCollectionFacade;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class ApiClientCollectionTransformer
 */
class ApiClientCollectionTransformer extends AbstractTransformer
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

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_API_CLIENT)) {
            $facade->addLink('_self_add', $this->generateRoute('open_orchestra_backoffice_api_client_new'));
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
