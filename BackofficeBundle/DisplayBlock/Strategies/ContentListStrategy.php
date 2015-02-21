<?php

namespace OpenOrchestra\BackofficeBundle\DisplayBlock\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class ContentListStrategy
 */
class ContentListStrategy extends AbstractStrategy
{
    /**
     * Perform the show action for a block
     *
     * @param BlockInterface $block
     *
     * @return Response
     */
    public function show(BlockInterface $block)
    {
        $attributes = array(
            'id' => $block->getId(),
            'class' => $block->getClass(),
            'contentNodeId' => $block->getAttribute('contentNodeId'),
            'characterNumber' => $block->getAttribute('characterNumber'),
            'keywords' => $block->getAttribute('keywords'),
            'choiceType' => $block->getAttribute('choiceType'),
            'contentType' => $block->getAttribute('contentType'),
            'contentTemplate' => $block->getAttribute('contentTemplate'),
        );

        return $this->render('OpenOrchestraBackofficeBundle:Block/ContentList:show.html.twig', $attributes);
    }

    /**
     * Check if the strategy support this block
     *
     * @param BlockInterface $block
     *
     * @return boolean
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::CONTENT_LIST === $block->getComponent();
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'content_list';
    }
}
