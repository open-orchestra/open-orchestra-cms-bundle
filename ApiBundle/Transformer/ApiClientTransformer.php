<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\ApiClientFacade;
use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\UserBundle\Document\ApiClient;

/**
 * Class ApiClientTransformer
 */
class ApiClientTransformer extends AbstractTransformer
{
    /**
     * @param ApiClient $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new ApiClientFacade();
        $facade->id = $mixed->getId();
        $facade->name = $mixed->getName();
        $facade->trusted = $mixed->isTrusted();
        $facade->key = $mixed->getKey();
        $facade->secret = $mixed->getSecret();

        $facade->addLink(
            '_self_delete',
            $this->generateRoute(
                'open_orchestra_api_api_client_delete',
                array('apiClientId' => $mixed->getId())
            )
        );

        $facade->addLink(
            '_self_form',
            $this->generateRoute(
                'open_orchestra_backoffice_api_client_form',
                array('apiClientId' => $mixed->getId())
            )
        );

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
