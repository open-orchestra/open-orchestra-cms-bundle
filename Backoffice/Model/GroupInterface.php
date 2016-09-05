<?php

namespace OpenOrchestra\Backoffice\Model;

use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\GroupInterface as BaseGroupInterface;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;

/**
 * Interface GroupInterface
 */
interface GroupInterface extends BaseGroupInterface
{
    const SEPARATOR_KEY_MODEL_ROLES = '##';

    /**
     * @param ReadSiteInterface|null $site
     */
    public function setSite(ReadSiteInterface $site = null);

    /**
     * @return ReadSiteInterface|null
     */
    public function getSite();

    /**
     * @param string $language
     * @param string $label
     */
    public function addLabel($language, $label);

    /**
     * @param string $language
     */
    public function removeLabel($language);

    /**
     * @param string $language
     *
     * @return string
     */
    public function getLabel($language);

    /**
     * @return array
     */
    public function getLabels();

    /**
     * @param array $labels
     */
    public function setLabels(array $labels);

    /**
     * @return array
     */
    public function getModelGroupRoles();

    /**
     * @param ModelGroupRoleInterface $modelGroupRole
     */
    public function addModelGroupRole(ModelGroupRoleInterface $modelGroupRole);

    /**
     * @param ArrayCollection <ModelGroupRoleInterface> $modelGroupRole
     */
    public function setModelGroupRoles(Collection $modelGroupRoles);

    /**
     * @param string $type
     * @param string $id
     * @param string $role
     *
     * @return ModelGroupRoleInterface|null
     */
    public function getModelGroupRoleByTypeAndIdAndRole($type, $id, $role);

    /**
     * @param string $type
     * @param string $id
     * @param string $role
     *
     * @return boolean
     */
    public function hasModelGroupRoleByTypeAndIdAndRole($type, $id, $role);

}
