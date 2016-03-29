<?php

namespace OpenOrchestra\Backoffice\UsageFinder;

use OpenOrchestra\ModelInterface\Model\RoleInterface;
use OpenOrchestra\ModelInterface\Repository\RoleableElementRepositoryInterface;

/**
 * Class RoleUsageFinder
 */
class RoleUsageFinder
{
    protected $roleableElementRepositories = array();

    /**
     * @param RoleableElementRepositoryInterface $repository
     */
    public function addRepository(RoleableElementRepositoryInterface $repository)
    {
        $this->roleableElementRepositories[] = $repository;
    }

    /**
     * @param RoleInterface $Role
     *
     * @return bool
     */
    public function hasUsage(RoleInterface $Role)
    {
        /** @var RoleableElementRepositoryInterface $repository */
        foreach ($this->roleableElementRepositories as $repository) {
            if ($repository->hasElementWithRole($Role)) {
                return true;
            }
        }

        return false;
    }
}
