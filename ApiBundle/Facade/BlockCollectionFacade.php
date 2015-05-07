<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

/**
 * Class BlockCollectionFacade
 */
class BlockCollectionFacade extends AbstractFacade
{

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\BlockFacade>")
     */
    protected $blocks = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addBlock(FacadeInterface $facade)
    {
        $this->blocks[] = $facade;
    }
}
