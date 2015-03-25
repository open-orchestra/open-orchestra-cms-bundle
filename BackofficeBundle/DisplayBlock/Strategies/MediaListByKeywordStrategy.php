<?php

namespace OpenOrchestra\BackofficeBundle\DisplayBlock\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\MediaBundle\DisplayBlock\Strategies\MediaListByKeywordStrategy as BaseMediaListByKeywordStrategy;

/**
 * Class MediaListByKeywordStrategy
 */
class MediaListByKeywordStrategy extends AbstractStrategy
{
    /**
     * Check if the strategy support this block
     *
     * @param ReadBlockInterface $block
     *
     * @return boolean
     */
    public function support(ReadBlockInterface $block)
    {
        return BaseMediaListByKeywordStrategy::MEDIA_LIST_BY_KEYWORD == $block->getComponent();
    }

    /**
     * Perform the show action for a block
     *
     * @param ReadBlockInterface $block
     *
     * @return Response
     */
    public function show(ReadBlockInterface $block)
    {
        return $this->render(
            'OpenOrchestraBackofficeBundle:Block/MediaList:show.html.twig',
            array(
                'id' => $block->getId(),
                'class' => $block->getClass(),
                'keywords' => $block->getAttribute('keywords')
            )
        );
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'media_list_by_keyword';
    }
}
