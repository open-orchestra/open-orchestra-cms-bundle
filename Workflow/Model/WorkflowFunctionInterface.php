<?php

namespace OpenOrchestra\Workflow\Model;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\ModelInterface\Model\BlameableInterface;
use OpenOrchestra\ModelInterface\Model\TimestampableInterface;
use OpenOrchestra\ModelInterface\Model\RoleInterface;

/**
 * Interface WorkflowFunctionInterface
 */
interface WorkflowFunctionInterface extends TimestampableInterface, BlameableInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $language
     * @param string $name
     */
    public function addName($language, $name);

    /**
     * @param string $language
     */
    public function removeName($language);

    /**
     * @param string $language
     *
     * @return string
     */
    public function getName($language);

    /**
     * @return array
     */
    public function getNames();

    /**
     * @param array $names
     */
    public function setNames(array $names);

    /**
     * @return Collection
     */
    public function getRoles();

    /**
     * @param RoleInterface $role
     */
    public function addRole(RoleInterface $role);

    /**
     * @param RoleInterface $role
     */
    public function removeRole(RoleInterface $role);
}
