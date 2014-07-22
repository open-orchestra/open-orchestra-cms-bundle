<?php

namespace PHPOrchestra\CMSBundle\DisplayBlock\Strategies;
use Mandango\Mandango;
use PHPOrchestra\CMSBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\CMSBundle\Model\Block;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class SampleStrategy
 */
class SampleStrategy extends AbstractStrategy
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
        return DisplayBlockInterface::SAMPLE == $block->getComponent();
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
        $datetime = time();
        $attributes = $block->getAttributes();

        $response = $this->render(
            'PHPOrchestraCMSBundle:Block/Sample:show.html.twig',
            array(
                'title' => $attributes['title'],
                'author' => $attributes['author'],
                'news' => $attributes['news'],
                'parameters' => $attributes['_page_parameters'],
                'datetime' => $datetime,
            )
        );

        $response->setPublic();
        $response->setSharedMaxAge(5);
        $response->headers->addCacheControlDirective('must-revalidate', true);

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
        $attributes = $block->getAttributes();

        return $this->render(
            'PHPOrchestraCMSBundle:Block/Sample:showBack.html.twig',
            array(
                'title' => $attributes['title'],
                'author' => $attributes['author'],
                'news' => $attributes['news'],
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
