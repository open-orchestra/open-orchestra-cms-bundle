<?php

namespace OpenOrchestra\Backoffice\UsageFinder;

use OpenOrchestra\ModelInterface\Model\RoleInterface;
use OpenOrchestra\ModelInterface\Repository\RoleableElementRepositoryInterface;

/**
 * Class RoleUsageFinder
 */
class RoleUsageFinder
{
    protected $RoleableElementRepositories = array();

    /**
     * @param RoleableElementRepositoryInterface $repository
     */
    public function addRepository(RoleableElementRepositoryInterface $repository)
    {
        $this->RoleableElementRepositories[] = $repository;
    }

    /**
     * @param RoleInterface $Role
     *
     * @return bool
     */
    public function hasUsage(RoleInterface $Role)
    {
        /** @var RoleableElementRepositoryInterface $repository */
        foreach ($this->RoleableElementRepositories as $repository) {
            if ($repository->hasElementWithRole($Role)) {
                return true;
            }
        }

        return false;
    }
}
