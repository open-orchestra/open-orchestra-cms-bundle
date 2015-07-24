<?php

namespace OpenOrchestra\Backoffice\BlockParameter\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\FooterStrategy as BaseFooterStrategy;
use OpenOrchestra\Backoffice\BlockParameter\BlockParameterInterface;
use OpenOrchestra\ModelInterface\Model\BlockInterface;

/**
 * Class FooterStrategy
 */
class FooterStrategy implements BlockParameterInterface
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseFooterStrategy::FOOTER == $block->getComponent();
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
        return 'footer';
    }
}
