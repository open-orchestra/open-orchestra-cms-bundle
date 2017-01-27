<?php

namespace OpenOrchestra\BackofficeBundle\Command;

/**
 * Class OrchestraUnpublishContentCommand
 */
class OrchestraUnpublishContentCommand extends OrchestraUnpublishElementCommand
{
    /**
     * @return string
     */
    public function getElementType()
    {
        return 'content';
    }

    /**
     * @return string
     */
    public function getManagerName()
    {
        return 'open_orchestra_cms.manager.content_publisher';
    }
}
