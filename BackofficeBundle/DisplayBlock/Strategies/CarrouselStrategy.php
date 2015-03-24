<?php

namespace OpenOrchestra\BackofficeBundle\DisplayBlock\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\CarrouselStrategy as BaseCarrouselStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CarrouselStrategy
 */
class CarrouselStrategy extends AbstractStrategy
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
        return BaseCarrouselStrategy::CARROUSEL == $block->getComponent();
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
        return $this->render('OpenOrchestraBackofficeBundle:Block/Carrousel:show.html.twig');
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'carrousel';
    }
}
