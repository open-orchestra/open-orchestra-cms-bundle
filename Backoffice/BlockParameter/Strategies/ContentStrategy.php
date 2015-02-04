<?php

namespace PHPOrchestra\Backoffice\BlockParameter\Strategies;

use PHPOrchestra\Backoffice\BlockParameter\BlockParameterInterface;
use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;

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
