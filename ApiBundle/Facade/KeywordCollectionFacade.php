<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class KeywordCollection
 */
class KeywordCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'keywords';

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\KeywordFacade>")
     */
    protected $keywords = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addKeyword(FacadeInterface $facade)
    {
        $this->keywords[] = $facade;
    }
}
