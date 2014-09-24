<?php

namespace PHPOrchestra\BackofficeBundle\DisplayBlock\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use PHPOrchestra\ModelBundle\Repository\NodeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class FooterStrategy
 */
class FooterStrategy extends AbstractStrategy
{
    protected $nodeRepository;
    protected $router;

    /**
     * @param NodeRepository        $nodeRepository
     * @param UrlGeneratorInterface $router
     */
    public function __construct(NodeRepository $nodeRepository, UrlGeneratorInterface $router)
    {
        $this->nodeRepository = $nodeRepository;
        $this->router = $router;
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
        return DisplayBlockInterface::FOOTER == $block->getComponent();
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
        $attributes = $block->getAttributes();

        return $this->render('PHPOrchestraBackofficeBundle:Block/Footer:show.html.twig',
            array(
                'id' => $attributes['id'],
                'class' => implode(' ', $attributes['class'])
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
        return 'footer';
    }
}
