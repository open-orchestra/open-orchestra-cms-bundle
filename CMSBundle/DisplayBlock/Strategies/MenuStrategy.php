<?php

namespace PHPOrchestra\CMSBundle\DisplayBlock\Strategies;

use Mandango\Mandango;
use PHPOrchestra\CMSBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\CMSBundle\Model\Block;
use PHPOrchestra\CMSBundle\Model\NodeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class MenuStrategy
 */
class MenuStrategy extends AbstractStratey
{
    protected $mandango;
    protected $router;

    /**
     * @param Mandango              $mandango
     * @param UrlGeneratorInterface $router
     */
    public function __construct(Mandango $mandango, UrlGeneratorInterface $router)
    {
        $this->mandango = $mandango;
        $this->router = $router;
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
        return DisplayBlockInterface::MENU == $block->getComponent();
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
        /** @var NodeRepository $repository */
        $repository = $this->mandango->getRepository('Model\PHPOrchestraCMSBundle\Node');
        $tree = $repository->getFooterTree();
        $tree = $repository->getTreeUrl($tree, $this->router);
        $attributes = $block->getAttributes();

        return $this->render(
            'PHPOrchestraCMSBundle:Block/Menu:show.html.twig',
            array(
                'tree' => $tree,
                'id' => $attributes['id'],
                'class' => $attributes['class'],
            )
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
        /** @var NodeRepository $repository */
        $repository = $this->mandango->getRepository('Model\PHPOrchestraCMSBundle\Node');
        $tree = $repository->getFooterTree();
        $tree = $repository->getTreeUrl($tree, $this->router);
        $attributes = $block->getAttributes();

        return $this->render(
            'PHPOrchestraCMSBundle:Block/Menu:showBack.html.twig',
            array(
                'tree' => $tree,
                'id' => $attributes['id'],
                'class' => $attributes['class'],
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
        return 'menu';
    }

}
