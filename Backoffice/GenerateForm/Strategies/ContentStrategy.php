<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ContentStrategy as BaseContentStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;

/**
 * Class ContentStrategy
 */
class ContentStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseContentStrategy::NAME === $block->getComponent();
    }

    /**
     * @return array
     */
    public function getRequiredUriParameter()
    {
        return array('contentId');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content';
    }
}
