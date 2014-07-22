<?php

namespace PHPOrchestra\CMSBundle\DisplayBlock\Strategies;
use Mandango\Mandango;
use PHPOrchestra\CMSBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\CMSBundle\Model\Block;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class NewsStrategy
 */
class NewsStrategy extends AbstractStrategy
{
    protected $mandango;

    public function __construct(Mandango $mandango)
    {
        $this->mandango = $mandango;
    }

    /**
     * Check if the strategy support this block
     *
     * @param Block $block
     *
     * @return boolean
     */
    public function support(Block $block)
    {
        return DisplayBlockInterface::NEWS == $block->getComponent();
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
        $allNews = $this->mandango->getRepository('Model\PHPOrchestraCMSBundle\Content')->getAllNews();

        return $this->render(
            'PHPOrchestraCMSBundle:Block/News:show.html.twig',
            array('allNews' => $allNews)
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
        $allNews = $this->mandango->getRepository('Model\PHPOrchestraCMSBundle\Content')->getAllNews();

        return $this->render(
            'PHPOrchestraCMSBundle:Block/News:showBack.html.twig',
            array('allNews' => $allNews)
        );
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'news';
    }
}
