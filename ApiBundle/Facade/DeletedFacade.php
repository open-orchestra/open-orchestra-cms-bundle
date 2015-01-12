<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use PHPOrchestra\ApiBundle\Facade\Traits\BaseFacade;
use PHPOrchestra\ApiBundle\Facade\Traits\TimestampableFacade;

/**
 * Class DeletedFacade
 *
 * @Serializer\Discriminator(field = "type", map = {
 *      "node": "PHPOrchestra\ApiBundle\Facade\NodeFacade",
 *      "content": "PHPOrchestra\ApiBundle\Facade\ContentFacade"
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
