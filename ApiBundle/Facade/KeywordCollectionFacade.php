<?php

namespace OpenOrchestra\ApiBundle\Facade;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

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
