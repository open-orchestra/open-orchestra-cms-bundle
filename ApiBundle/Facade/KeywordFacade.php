<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class KeywordFacade
 */
class KeywordFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $label;
}
