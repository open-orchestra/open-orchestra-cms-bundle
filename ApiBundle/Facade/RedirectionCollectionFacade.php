<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class RedirectionCollection
 */
class RedirectionCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'redirections';

    /**
     * @Serializer\Type("array<PHPOrchestra\ApiBundle\Facade\RedirectionFacade>")
     */
    protected $redirections = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addRedirection(FacadeInterface $facade)
    {
        $this->redirections[] = $facade;
    }
}
