<?php

namespace OpenOrchestra\BackofficeBundle\Command;

/**
 * Class OrchestraPublishContentCommand
 */
class OrchestraPublishContentCommand extends OrchestraPublishElementCommand
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
