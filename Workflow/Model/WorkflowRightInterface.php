<?php

namespace OpenOrchestra\Workflow\Model;

use Doctrine\Common\Collections\Collection;

/**
 * Interface WorkflowRightInterface
 */
interface WorkflowRightInterface
{
    const NODE = 'open_orchestra_workflow_function.node';

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $userId
     */
    public function setUserId($userId);

    /**
     * @return string
     */
    public function getUserId();

    /**
     * @param Collection $authorizations
     */
    public function setAuthorizations(Collection $authorizations);

    /**
     * @return Collection
     */
    public function getAuthorizations();

    /**
     * @param AuthorizationInterface
     */
    public function removeAuthorization(AuthorizationInterface $authorization);

    /**
     * @param AuthorizationInterface
     */
    public function addAuthorization(AuthorizationInterface $authorization);
}
