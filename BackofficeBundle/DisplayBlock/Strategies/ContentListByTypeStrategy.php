<?php

namespace PHPOrchestra\BackofficeBundle\DisplayBlock\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;

/**
 * Class ContentListByTypeStrategy
 */
class ContentListByTypeStrategy extends AbstractContentListStrategy
{
    /**
     * Check if the strategy support this block
     *
     * @param BlockInterface $block
     *
     * @return boolean
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::CONTENT_LIST_BY_TYPE== $block->getComponent();
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'content_list_by_type';
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    protected function generateParameters($attributes)
    {
        return array('contentType' => $attributes['contentType']);
    }
}
