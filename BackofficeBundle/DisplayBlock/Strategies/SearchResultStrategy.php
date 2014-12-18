<?php

namespace PHPOrchestra\BackofficeBundle\DisplayBlock\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use PHPOrchestra\ModelInterface\Model\BlockInterface;

/**
 * Class SearchResultStrategy
 */
class SearchResultStrategy extends AbstractStrategy
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
        return DisplayBlockInterface::SEARCH_RESULT == $block->getComponent();
    }

    /**
     * Perform the show action for a block
     *
     * @param BlockInterface $block
     *
     * @return Response
     */
    public function show(BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        return $this->render(
            'PHPOrchestraBackofficeBundle:Block/SearchResult:show.html.twig',
            array(
                'nodeId' => $attributes['nodeId'],
                'nbdoc' => $attributes['nbdoc'],
                'fielddisplayed' => implode(', ', $attributes['fielddisplayed']),
                'nbfacet' => count($attributes['facets']),
                'nbfilter' => count($attributes['filter'])
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
        return 'search_result';
    }
}
