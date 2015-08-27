<?php

namespace OpenOrchestra\Backoffice\RestoreEntity\Strategies;

use OpenOrchestra\Backoffice\RestoreEntity\RestoreEntityInterface;
use OpenOrchestra\BackofficeBundle\Manager\ContentManager;
use OpenOrchestra\ModelInterface\Model\ContentInterface;

/**
 * Class RestoreContentStrategy
 */
class RestoreContentStrategy implements RestoreEntityInterface
{
    /**
     * @var ContentManager
     */
    protected $nodeManager;

    /**
     * @param ContentManager $contentManager
     */
    public function __construct(ContentManager $contentManager)
    {
        $this->contentManager = $contentManager;
    }

    /**
     * @param mixed $entity
     *
     * @return bool
     */
    public function support($entity)
    {
        return $entity instanceof ContentInterface;
    }

    /**
     * @param ContentInterface $content
     */
    public function restore($content)
    {
        $this->contentManager->restoreContent($content);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'restore_content';
    }
}
