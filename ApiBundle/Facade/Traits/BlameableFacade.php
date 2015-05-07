<?php

namespace OpenOrchestra\ApiBundle\Facade\Traits;

/**
 * Class BlameableFacade
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 */
trait BlameableFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $createdBy;

    /**
     * @Serializer\Type("string")
     */
    public $updatedBy;
}
