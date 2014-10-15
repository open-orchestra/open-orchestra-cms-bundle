<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\NodeCollectionFacade;

/**
 * Class NodeCollectionTransformer
 */
class NodeCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     *
     * @return FacadeInterface
     */
    public function transformVersions($mixed)
    {
        $facade = new NodeCollectionFacade();

        foreach ($mixed as $node) {
            $facade->addNode($this->getTransformer('node')->transformVersion($node));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'node_collection';
    }
}
