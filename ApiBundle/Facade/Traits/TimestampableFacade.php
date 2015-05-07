<?php

namespace OpenOrchestra\ApiBundle\Facade\Traits;

/**
 * Trait TimestampableFacade
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 */
trait TimestampableFacade
{
    /**
     * @Serializer\Type("DateTime<'d/m/Y H:i:s'>")
     */
    public $createdAt;

    /**
     * @Serializer\Type("DateTime<'d/m/Y H:i:s'>")
     */
    public $updatedAt;
}
