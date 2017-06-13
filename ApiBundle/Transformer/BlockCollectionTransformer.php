<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class BlockCollectionTransformer
 */
class BlockCollectionTransformer extends AbstractTransformer
{
    /**
     * @param Collection      $blockCollection
     *
     * @return FacadeInterface
     */
    public function transform($blockCollection)
    {
        $facade = $this->newFacade();

        foreach ($blockCollection as $block) {
            $facade->addBlock($this->getTransformer('block')->transform($block));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'block_collection';
    }
}
