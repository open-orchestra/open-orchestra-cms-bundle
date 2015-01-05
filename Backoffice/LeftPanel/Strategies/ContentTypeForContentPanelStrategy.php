<?php

namespace PHPOrchestra\Backoffice\LeftPanel\Strategies;

use PHPOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;

/**
 * Class ContentTypeForContentPanel
 */
class ContentTypeForContentPanelStrategy extends AbstractLeftPaneStrategy
{
    /**
     * @var ContentTypeRepositoryInterface
     */
    protected $contentTypeRepository;

    /**
     * @param ContentTypeRepositoryInterface $contentTypeRepository
     */
    public function __construct(ContentTypeRepositoryInterface $contentTypeRepository)
    {
        $this->contentTypeRepository = $contentTypeRepository;
    }

    /**
     * @return string
     */
    public function show()
    {
        $contentTypes = $this->contentTypeRepository->findAllByDeletedInLastVersion();

        return $this->render(
            'PHPOrchestraBackofficeBundle:Tree:showContentTypeForContent.html.twig',
            array(
                'contentTypes' => $contentTypes,
            )
        );
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return self::EDITORIAL;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content_type_for_content';
    }
}
