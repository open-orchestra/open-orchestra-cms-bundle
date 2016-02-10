<?php

namespace OpenOrchestra\BackofficeBundle\DisplayBlock\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\SampleStrategy as BaseSampleStrategy;
use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SampleStrategy
 */
class SampleStrategy extends AbstractStrategy
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
        return BaseSampleStrategy::NAME == $block->getComponent();
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
            'OpenOrchestraBackofficeBundle:Block/Sample:show.html.twig',
            array(
                'title' => $block->getAttribute('title'),
                'author' => $block->getAttribute('author')
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
        return 'sample';
    }
}
