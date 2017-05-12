<?php

namespace OpenOrchestra\Backoffice\DisplayBlock\Strategies;

use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultStrategy
 */
class DefaultStrategy extends AbstractDisplayBlockStrategy
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
        return true;
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
            'OpenOrchestraBackofficeBundle:Block/Default:show.html.twig',
            array('label' => $block->getLabel())
        );
    }

    /**
     * @param ReadBlockInterface $block
     *
     * @return string
     */
    public function toString(ReadBlockInterface $block)
    {
        return '';
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'default';
    }
}
