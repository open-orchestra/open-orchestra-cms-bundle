<?php

namespace PHPOrchestra\BackofficeBundle\DisplayBlock\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContentListStrategy
 */
class ContentListStrategy extends AbstractStrategy
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

        $parameters = array(
            'id' => $block->getId(),
            'class' => $block->getClass(),
            'contentNodeId' => array_key_exists('contentNodeId', $attributes) ? $attributes['contentNodeId'] : '',
            'characterNumber' => array_key_exists('characterNumber', $attributes) ? $attributes['characterNumber'] : '',
            'keywords' => $attributes['keywords'],
            'choiceType' => array_key_exists('choiceType', $attributes) ? $attributes['choiceType'] : '',
            'contentType' => array_key_exists('contentType', $attributes) ? $attributes['contentType'] : '',
        );
        return $this->render('PHPOrchestraBackofficeBundle:Block/ContentList:show.html.twig', $parameters);
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
        return DisplayBlockInterface::CONTENT_LIST === $block->getComponent();
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'content_list';
    }
}
