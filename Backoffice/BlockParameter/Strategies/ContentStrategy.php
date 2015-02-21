<?php

namespace OpenOrchestra\Backoffice\BlockParameter\Strategies;

use OpenOrchestra\Backoffice\BlockParameter\BlockParameterInterface;
use OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
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
        return DisplayBlockInterface::CONTENT == $block->getComponent();
    }

    /**
     * @return array
     */
    public function getBlockParameter()
    {
        return array('newsId');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content';
    }

}
