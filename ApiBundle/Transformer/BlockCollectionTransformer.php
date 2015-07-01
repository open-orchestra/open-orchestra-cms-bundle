<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ApiBundle\Facade\BlockCollectionFacade;

/**
 * Class BlockCollectionTransformer
 */
class BlockCollectionTransformer extends AbstractTransformer
{
    /**
     * @param \Doctrine\Common\Collections\Collection      $blockCollection
     * @param \Doctrine\Common\Collections\Collection|null $generateMixed
     * @param string|null                                  $nodeId
     *
     * @return \OpenOrchestra\BaseApi\Facade\FacadeInterface
     */
    public function transform($blockCollection, $generateMixed = null, $nodeId = null)
    {
        $facade = new BlockCollectionFacade();

        if (null !== $generateMixed) {
            foreach($generateMixed as $block) {
                $facade->addBlock($this->getTransformer('block')->transform($block, true));
            }
        }

        foreach ($blockCollection as $key => $block) {
            $facade->addBlock($this->getTransformer('block')->transform($block, false, $nodeId, $key));
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
