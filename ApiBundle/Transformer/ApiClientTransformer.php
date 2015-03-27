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
        $facade->name = $mixed->getName();
        $facade->trusted = $mixed->isTrusted();

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
