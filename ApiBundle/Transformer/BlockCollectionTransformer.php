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
     * @param Collection|null $generateMixed
     *
     * @return FacadeInterface
     */
    public function transform($blockCollection, $generateMixed = null)
    {
        $facade = $this->newFacade();

        if (null !== $generateMixed) {
            foreach($generateMixed as $block) {
                $facade->addBlock($this->getTransformer('block')->transform($block, true));
            }
        }

        foreach ($blockCollection as $block) {
            $facade->addBlock($this->getTransformer('block')->transform($block, false));
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
