<?php

namespace PHPOrchestra\CMSBundle\DisplayBlock\Strategies;

use Mandango\Mandango;
use PHPOrchestra\CMSBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SampleStrategy
 */
class SampleStrategy extends AbstractStrategy
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
        return DisplayBlockInterface::SAMPLE == $block->getComponent();
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
        $datetime = time();
        $attributes = $block->getAttributes();

        $response = $this->render(
            'PHPOrchestraCMSBundle:Block/Sample:show.html.twig',
            array(
                'title' => $attributes['title'],
                'author' => $attributes['author'],
                'news' => $attributes['news'],
                'parameters' => array(),
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
     * @param BlockInterface $block
     *
     * @return Response
     */
    public function showBack(BlockInterface $block)
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
