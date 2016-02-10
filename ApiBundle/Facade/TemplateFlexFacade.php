<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class TemplateFlexFacade
 */
class TemplateFlexFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $siteId;

    /**
     * @Serializer\Type("string")
     */
    public $templateId;

    /**
     * @Serializer\Type("string")
     */
    public $name;

    /**
     * @Serializer\Type("boolean")
     */
    public $deleted;

    /**
     * @Serializer\Type("OpenOrchestra\ApiBundle\Facade\AreaFlexFacade")
     */
    public $area;

    /**
     * @Serializer\Type("boolean")
     */
    public $editable;
}
