<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

/**
 * Class BlockComponentCollectionFacade
 */
class BlockComponentCollectionFacade implements FacadeInterface
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'block_components';

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\BlockComponentFacade>")
     */
    protected $blockComponents = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addBlockComponents(FacadeInterface $facade)
    {
        $this->blockComponents[] = $facade;
    }
}
