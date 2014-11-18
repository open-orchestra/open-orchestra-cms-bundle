<?php

namespace PHPOrchestra\BackofficeBundle\DisplayBlock\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class MenuStrategy
 */
class MenuStrategy extends AbstractStrategy
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
        return DisplayBlockInterface::MENU == $block->getComponent();
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
            'PHPOrchestraBackofficeBundle:Block/Menu:show.html.twig',
            array(
                'id' => array_key_exists('id', $attributes)? $attributes['id']: '',
                'class' => array_key_exists('class', $attributes)? $attributes['class']: '',
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
        return 'menu';
    }
}
