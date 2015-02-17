<?php

namespace PHPOrchestra\BackofficeBundle\DisplayBlock\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VideoStrategy
 */
class VideoStrategy extends AbstractStrategy
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
        return DisplayBlockInterface::VIDEO === $block->getComponent();
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
        return $this->render(
            'PHPOrchestraBackofficeBundle:Block/Video:show.html.twig',
            array('attributes' => $block->getAttributes())
        );
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'video';
    }
}
