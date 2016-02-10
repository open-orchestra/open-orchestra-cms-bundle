<?php

namespace OpenOrchestra\BackofficeBundle\DisplayBlock\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy as BaseTinyMCEWysiwygStrategy;
use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TinyMCEWysiwygStrategy
 */
class TinyMCEWysiwygStrategy extends AbstractStrategy
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
        return BaseTinyMCEWysiwygStrategy::NAME == $block->getComponent();
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
        $htmlContent = $block->getAttribute('htmlContent');

        return $this->render(
            'OpenOrchestraBackofficeBundle:Block/TinyMCEWysiwyg:show.html.twig',
            array('htmlContent' => strip_tags($htmlContent))
        );
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'TinyMCEWysiwyg';
    }
}
