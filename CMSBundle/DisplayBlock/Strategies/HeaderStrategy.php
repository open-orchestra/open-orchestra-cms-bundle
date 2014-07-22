<?php

namespace PHPOrchestra\CMSBundle\DisplayBlock\Strategies;
use PHPOrchestra\CMSBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\CMSBundle\Model\Block;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class HeaderStrategy
 */
class HeaderStrategy extends AbstractStrategy
{
    /**
     * Check if the strategy support this block
     *
     * @param Block $block
     *
     * @return boolean
     */
    public function support(Block $block)
    {
        return DisplayBlockInterface::HEADER == $block->getComponent();
    }

    /**
     * Perform the show action for a block
     *
     * @param Block $block
     *
     * @return Response
     */
    public function show(Block $block)
    {
        $attributes = $block->getAttributes();

        return $this->render(
            'PHPOrchestraCMSBundle:Block/Header:show.html.twig',
            array(
                'id' => $attributes['id'],
                'class' => $attributes['class'],
                'logo' => $attributes['logo']
            )
        );
    }

    /**
     * Perform the show action for a block on the backend
     *
     * @param Block $block
     *
     * @return Response
     */
    public function showBack(Block $block)
    {
        $attributes = $block->getAttributes();

        return $this->render(
            'PHPOrchestraCMSBundle:Block/Header:show.html.twig',
            array(
                'id' => $attributes['id'],
                'class' => $attributes['class'],
                'logo' => $attributes['logo']
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
        return 'header';
    }
}
