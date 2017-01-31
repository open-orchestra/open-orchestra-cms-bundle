<?php

namespace OpenOrchestra\Backoffice\Manager;

use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;

/**
 * Interface AutoPublishManager
 */
Interface AutoPublishManagerInterface
{
    const ERROR_NO_PUBLISH_FROM_STATUS = 1;
    const ERROR_NO_PUBLISHED_STATUS = 2;
    const ERROR_NO_UNPUBLISHED_STATUS = 3;

    /**
     * @param ReadSiteInterface $site
     *
     * @return array|int   A published element list or an error code
     */
    public function publishElements(ReadSiteInterface $site);

    /**
     * @param ReadSiteInterface $site
     *
     * @return array|int   An unpublished element list or an error code
     */
    public function unpublishElements(ReadSiteInterface $site);
}
