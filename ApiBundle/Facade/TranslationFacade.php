<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

/**
 * Class TranslationFacade
 */
class TranslationFacade implements FacadeInterface
{
    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("locale")
     */
    public $locale;

    /**
     * @Serializer\Type("array<string,string>")
     * @Serializer\SerializedName("catalog")
     */
    public $catalog = array();
}
