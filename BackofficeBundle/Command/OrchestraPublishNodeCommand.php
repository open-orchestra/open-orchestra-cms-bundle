<?php

namespace OpenOrchestra\BackofficeBundle\Command;

/**
 * Class OrchestraPublishNodeCommand
 */
class OrchestraPublishNodeCommand extends OrchestraPublishElementCommand
{
    /**
     * @return string
     */
    public function getElementType()
    {
        return 'node';
    }

    /**
     * @return string
     */
    public function getManagerName()
    {
        return 'open_orchestra_cms.manager.node_publisher';
    }
}
