<?php

namespace OpenOrchestra\Backoffice\BlockParameter\Strategies;

use OpenOrchestra\Backoffice\BlockParameter\BlockParameterInterface;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ContentStrategy as BaseContentStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;

/**
 * Class ContentStrategy
 */
class ContentStrategy implements BlockParameterInterface
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseContentStrategy::NAME == $block->getComponent();
    }

    /**
     * @return array
     */
    public function getBlockParameter()
    {
        return array('request.contentId');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content';
    }
}
