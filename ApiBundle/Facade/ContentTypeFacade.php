<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class ContentTypeFacade
 */
class ContentTypeFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $contentTypeId;

    /**
     * @Serializer\Type("string")
     */
    public $name;

    /**
     * @Serializer\Type("integer")
     */
    public $version;

    /**
     * @Serializer\Type("OpenOrchestra\ApiBundle\Facade\StatusFacade")
     */
    public $status;

    /**
     * @Serializer\Type("string")
     */
    public $statusLabel;

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\FieldTypeFacade>")
     */
    protected $fields = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addField(FacadeInterface $facade)
    {
        $this->fields[] = $facade;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }
}
