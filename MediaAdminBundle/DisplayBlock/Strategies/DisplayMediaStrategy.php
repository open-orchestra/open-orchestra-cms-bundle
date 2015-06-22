<?php

namespace OpenOrchestra\MediaAdminBundle\DisplayBlock\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\Media\DisplayBlock\Strategies\DisplayMediaStrategy as BaseMediaStrategy;

/**
 * Class DisplayMediaStrategy
 */
class DisplayMediaStrategy extends AbstractStrategy
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
       return BaseMediaStrategy::DISPLAY_MEDIA == $block->getComponent();
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
        $parameters = array(
            'imageFormat' => $block->getAttribute('imageFormat'),
            'nodeToLink' => $block->getAttribute('nodeToLink'),
        );

        return $this->render('OpenOrchestraMediaAdminBundle:Block/DisplayMedia:show.html.twig', $parameters);
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return "display_media";
    }

}
