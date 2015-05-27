<?php

namespace OpenOrchestra\Backoffice\LeftPanel\Strategies;

use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;

/**
 * Class ContentTypeForContentPanel
 */
class ContentTypeForContentPanelStrategy extends AbstractLeftPanelStrategy
{
    const ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT = 'ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT';

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
        ksort($contentTypes);
        return $this->render(
            'OpenOrchestraBackofficeBundle:Tree:showContentTypeForContent.html.twig',
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

    /**
     * @return string
     */
    public function getRole()
    {
        return self::ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT;
    }
}
