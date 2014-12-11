<?php

namespace PHPOrchestra\BackofficeBundle\DisplayIcons\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;

/**
 * Class ContentListByKeywordIconStrategy
 */
class ContentListByKeywordIconStrategy extends AbstractContentListIconStrategy
{
    /**
     * Check if the strategy support this block
     *
     * @param string $block
     *
     * @return boolean
     */
    public function support($block)
    {
        return DisplayBlockInterface::CONTENT_LIST_BY_KEYWORD == $block;
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'content_list_by_keyword';
    }
}
