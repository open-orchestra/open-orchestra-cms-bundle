<?php

namespace PHPOrchestra\CMSBundle\DisplayBlock\Strategies;

use PHPOrchestra\CMSBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use PHPOrchestra\ModelBundle\Repository\ContentRepository;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NewsStrategy
 */
class NewsStrategy extends AbstractStrategy
{
    protected $contentRepository;

    /**
     * @param ContentRepository $contentRepository
     */
    public function __construct(ContentRepository $contentRepository)
    {
        $this->contentRepository = $contentRepository;
    }

    /**
     * Check if the strategy support this block
     *
     * @param BlockInterface $block
     *
     * @return boolean
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::NEWS == $block->getComponent();
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
        $allNews = $this->contentRepository->findAllNews();

        return $this->render(
            'PHPOrchestraCMSBundle:Block/News:show.html.twig',
            array('allNews' => $allNews)
        );
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
        $allNews = $this->contentRepository->findAllNews();

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
