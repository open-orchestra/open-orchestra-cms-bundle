<?php

namespace OpenOrchestra\Backoffice\BlockParameter\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\MenuStrategy as BaseMenuStrategy;
use OpenOrchestra\Backoffice\BlockParameter\BlockParameterInterface;
use OpenOrchestra\ModelInterface\Model\BlockInterface;

/**
 * Class MenuStrategy
 */
class MenuStrategy implements BlockParameterInterface
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseMenuStrategy::NAME == $block->getComponent();
    }

    /**
     * @return array
     */
    public function getBlockParameter()
    {
        return array('request.aliasId');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'menu';
    }
}
