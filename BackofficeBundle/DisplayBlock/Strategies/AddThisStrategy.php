<?php

namespace OpenOrchestra\BackofficeBundle\DisplayBlock\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AddThisStrategy as BaseAddThisStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AddThisStrategy
 */
class AddThisStrategy extends AbstractStrategy
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
        return BaseAddThisStrategy::ADDTHIS === $block->getComponent();
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
        $parameters = array(
            'pubid' => $block->getAttribute('pubid'),
            'addThisClass' => $block->getAttribute('addThisClass')
        );

        return $this->render('OpenOrchestraBackofficeBundle:Block/AddThis:show.html.twig', $parameters);
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'add_this';
    }
}
