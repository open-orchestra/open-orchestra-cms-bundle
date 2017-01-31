<?php

namespace OpenOrchestra\BackofficeBundle\Command;

/**
 * Class OrchestraUnpublishNodeCommand
 */
class OrchestraUnpublishNodeCommand extends OrchestraUnpublishElementCommand
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
