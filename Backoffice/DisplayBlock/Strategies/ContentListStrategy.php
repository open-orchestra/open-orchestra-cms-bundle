<?php

namespace OpenOrchestra\Backoffice\DisplayBlock\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ContentListStrategy as BaseContentListStrategy;
use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContentListStrategy
 */
class ContentListStrategy extends AbstractStrategy
{
    /**
     * Perform the show action for a block
     *
     * @param ReadBlockInterface $block
     *
     * @return Response
     */
    public function show(ReadBlockInterface $block)
    {
        $contentSearch = $block->getAttribute('contentSearch');
        $attributes = array(
            'id' => $block->getId(),
            'class' => $block->getClass(),
            'contentNodeId' => $block->getAttribute('contentNodeId'),
            'characterNumber' => $block->getAttribute('characterNumber'),
            'keywords' => (!is_null($contentSearch) && array_key_exists('keywords', $contentSearch)) ? $contentSearch['keywords'] : '',
            'choiceType' => (!is_null($contentSearch) && array_key_exists('choiceType', $contentSearch)) ? $contentSearch['choiceType'] : '',
            'contentType' => (!is_null($contentSearch) && array_key_exists('contentType', $contentSearch)) ? $contentSearch['contentType'] : '',
            'contentTemplateEnabled' => $block->getAttribute('contentTemplateEnabled'),
            'contentTemplate' => $block->getAttribute('contentTemplate'),
        );

        return $this->render('OpenOrchestraBackofficeBundle:Block/ContentList:show.html.twig', $attributes);
    }

    /**
     * Check if the strategy support this block
     *
     * @param ReadBlockInterface $block
     *
     * @return boolean
     */
    public function support(ReadBlockInterface $block)
    {
        return BaseContentListStrategy::NAME === $block->getComponent();
    }

    /**
     * @param ReadBlockInterface $block
     *
     * @return Array
     */
    public function getCacheTags(ReadBlockInterface $block)
    {
        return array();
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
