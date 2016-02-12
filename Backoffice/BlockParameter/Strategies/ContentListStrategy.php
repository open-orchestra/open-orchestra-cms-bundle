<?php

namespace OpenOrchestra\Backoffice\BlockParameter\Strategies;

use OpenOrchestra\Backoffice\BlockParameter\BlockParameterInterface;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ContentListStrategy as BaseContentListStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;

/**
 * Class ContentListStrategy
 */
class ContentListStrategy implements BlockParameterInterface
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseContentListStrategy::NAME == $block->getComponent();
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
        return 'content_list';
    }
}
