<?php

namespace OpenOrchestra\Backoffice\UsageFinder;

use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\StatusableElementRepositoryInterface;

/**
 * Class StatusUsageFinder
 */
class StatusUsageFinder
{
    protected $statusableElementRepositories = array();

    /**
     * @param StatusableElementRepositoryInterface $repository
     */
    public function addRepository(StatusableElementRepositoryInterface $repository)
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
        /** @var StatusableElementRepositoryInterface $repository */
        foreach ($this->statusableElementRepositories as $repository) {
            if ($repository->hasStatusedElement($status)) {
                return true;
            }
        }

        return false;
    }
}
