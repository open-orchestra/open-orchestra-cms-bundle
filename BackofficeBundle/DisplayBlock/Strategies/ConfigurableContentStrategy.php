<?php

namespace PHPOrchestra\BackofficeBundle\DisplayBlock\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use PHPOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ConfigurableContentStrategy
 */
class ConfigurableContentStrategy extends AbstractStrategy
{
    protected $contentRepository;

    /**
     * @param ContentRepositoryInterface $contentRepository
     */
    public function __construct(ContentRepositoryInterface $contentRepository)
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
        return DisplayBlockInterface::CONFIGURABLE_CONTENT == $block->getComponent();
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
        $content = $this->contentRepository->findOneByContentId($block->getAttribute('contentId'));

        $contentAttributes = array();
        if ($content) {
            $contentAttributes = $content->getAttributes();
        }

        return $this->render(
            'PHPOrchestraBackofficeBundle:Block/ConfigurableContent:show.html.twig',
            array('contentAttributes' => $contentAttributes)
        );
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'configurable_content';
    }
}
