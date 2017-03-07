<?php

namespace OpenOrchestra\Backoffice\UsageFinder;

use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\StatusableContainerRepositoryInterface;

/**
 * Class StatusUsageFinder
 */
class StatusUsageFinder
{
    protected $statusableElementRepositories = array();

    /**
     * @param StatusableContainerRepositoryInterface $repository
     */
    public function addRepository(StatusableContainerRepositoryInterface $repository)
    {
        $this->statusableElementRepositories[] = $repository;
    }

    /**
     * @param StatusInterface $status
     *
     * @return bool
     */
    public function hasUsage(StatusInterface $status)
    {
        /** @var StatusableContainerRepositoryInterface $repository */
        foreach ($this->statusableElementRepositories as $repository) {
            if ($repository->hasStatusedElement($status)) {
                return true;
            }
        }

        return false;
    }
}
