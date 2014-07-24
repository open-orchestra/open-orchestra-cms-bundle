<?php

namespace PHPOrchestra\CMSBundle\DisplayBlock\Strategies;

use PHPOrchestra\CMSBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
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
        return DisplayBlockInterface::CARROUSEL == $block->getComponent();
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
            'PHPOrchestraCMSBundle:Block/Carrousel:show.html.twig',
            $block->getAttributes()
        );
    }

    /**
     * Perform the show action for a block on the backend
     *
     * @param BlockInterface $block
     *
     * @return Response
     */
    public function showBack(BlockInterface $block)
    {
        return $this->render(
            'PHPOrchestraCMSBundle:Block/Carrousel:showBack.html.twig',
            $block->getAttributes()
        );
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
