<?php

namespace PHPOrchestra\BackofficeBundle\DisplayBlock\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractContentListStrategy
 */
abstract class AbstractContentListStrategy extends AbstractStrategy
{
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

        $parameters = $this->generateParameters($attributes);

        $parameters = array_merge(
            array(
                'id' => $attributes['id'],
                'class' => $attributes['class'],
                'url' => $attributes['url'],
                'characterNumber' => $attributes['characterNumber'],
            ),
            $parameters
        );
        return $this->render('PHPOrchestraBackofficeBundle:Block/ContentList:show.html.twig', $parameters);
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    abstract protected function generateParameters($attributes);
}
