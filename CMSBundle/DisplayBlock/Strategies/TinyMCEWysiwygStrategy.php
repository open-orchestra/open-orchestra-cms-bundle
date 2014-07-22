<?php

namespace PHPOrchestra\CMSBundle\DisplayBlock\Strategies;
use PHPOrchestra\CMSBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\CMSBundle\Model\Block;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class TinyMCEWysiwygStrategy
 */
class TinyMCEWysiwygStrategy extends AbstractStrategy
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
        return DisplayBlockInterface::TINYMCEWYSIWYG == $block->getComponent();
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
        $_htmlContent = $attributes['_htmlContent'];

        $response = $this->render(
            'PHPOrchestraCMSBundle:Block/TinyMCEWysiwyg:show.html.twig',
            array(
                'htmlContent' => $_htmlContent
            )
        );

        $response->setPublic();
        $response->setSharedMaxAge(0);
        return $response;
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
        return $this->show($block);
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
