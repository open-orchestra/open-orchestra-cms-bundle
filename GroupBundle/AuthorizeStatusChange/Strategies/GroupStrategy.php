<?php

namespace OpenOrchestra\GroupBundle\AuthorizeStatusChange\Strategies;

use OpenOrchestra\Backoffice\AuthorizeStatusChange\AuthorizeStatusChangeInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class GroupStrategy
 */
class GroupStrategy implements AuthorizeStatusChangeInterface
{
    protected $authorizationChecker;
    protected $roleRepository;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param RoleRepositoryInterface       $roleRepository
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, RoleRepositoryInterface $roleRepository)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param StatusableInterface $document
     * @param StatusInterface     $toStatus
     *
     * @return bool
     */
    public function isGranted(StatusableInterface $document, StatusInterface $toStatus)
    {
        $fromStatus = $document->getStatus();
        $role = $this->roleRepository->findOneByFromStatusAndToStatus($fromStatus, $toStatus);

        return !($role && !$this->authorizationChecker->isGranted(array($role->getName())));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'group';
    }
}
