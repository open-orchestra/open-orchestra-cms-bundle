<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class TranslationFacade
 */
class TranslationFacade
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
