<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class BlockCollectionFacade
 */
class BlockCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'blocks';

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\BlockFacade>")
     */
    protected $loadBlocks = array();

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\GenerateBlockFacade>")
     */
    protected $generateBlocks = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addLoadBlock(FacadeInterface $facade)
    {
        $this->loadBlocks[] = $facade;
    }

    /**
     * @param FacadeInterface $facade
     */
    public function addGenerateBlock(FacadeInterface $facade)
    {
        $this->generateBlocks[] = $facade;
    }
}
