<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\ApiBundle\Facade\Traits\BaseFacade;
use OpenOrchestra\ApiBundle\Facade\Traits\TimestampableFacade;

/**
 * Class DeletedFacade
 *
 * @Serializer\Discriminator(field = "type", map = {
 *      "node": "OpenOrchestra\ApiBundle\Facade\NodeFacade",
 *      "content": "OpenOrchestra\ApiBundle\Facade\ContentFacade"
 * })
 */
class DeletedFacade implements FacadeInterface
{
    use TimestampableFacade;
    use BaseFacade;

    /**
     * @Serializer\Type("boolean")
     */
    public $deleted;

    /**
     * @Serializer\Type("string")
     */
    public $name;
}
