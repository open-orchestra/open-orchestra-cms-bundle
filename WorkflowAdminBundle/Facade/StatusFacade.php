<?php

namespace OpenOrchestra\WorkflowAdminBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class StatusFacade
 */
class StatusFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("boolean")
     */
    public $publishedState;

    /**
     * @Serializer\Type("boolean")
     */
    public $initialState;

    /**
     * @Serializer\Type("boolean")
     */
    public $autoPublishFromState;

    /**
     * @Serializer\Type("boolean")
     */
    public $blockedEdtion;

    /**
     * @Serializer\Type("boolean")
     */
    public $autoUnpublishToState;

    /**
     * @Serializer\Type("boolean")
     */
    public $translationState;

    /**
     * @Serializer\Type("string")
     */
    public $name;

    /**
     * @Serializer\Type("string")
     */
    public $label;

    /**
     * @Serializer\Type("string")
     */
    public $displayColor;

    /**
     * @Serializer\Type("string")
     */
    public $codeColor;
}
