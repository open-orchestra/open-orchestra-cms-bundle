<?php

namespace OpenOrchestra\BackofficeBundle\Model;

/**
 * Interface GroupRoleInterface
 */
interface GroupRoleInterface
{
    const ACCESS_GRANTED = "granted";
    const ACCESS_DENIED = "denied";
    const ACCESS_INHERIT = "inherit";

    /**
     * @return string
     */
    public function getRole();

    /**
     * @return string
     */
    public function getAccessType();

    /**
     * @param string $role
     */
    public function setRole($role);

    /**
     * @param string $accessType
     */
    public function setAccessType($accessType);

    /**
     * @return bool
     */
    public function isGranted();

    /**
     * @param $granted
     */
    public function setGranted($granted);
}
