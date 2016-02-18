<?php

namespace OpenOrchestra\Backoffice\Model;

use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\GroupInterface as BaseGroupInterface;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\ModelInterface\Model\TranslatedValueContainerInterface;
use OpenOrchestra\ModelInterface\Model\TranslatedValueInterface;

/**
 * Interface GroupInterface
 */
interface GroupInterface extends BaseGroupInterface, TranslatedValueContainerInterface
{
    /**
     * @param ReadSiteInterface|null $site
     */
    public function setSite(ReadSiteInterface $site = null);

    /**
     * @return ReadSiteInterface|null
     */
    public function getSite();

    /**
     * @param TranslatedValueInterface $label
     */
    public function addLabel(TranslatedValueInterface $label);

    /**
     * @param TranslatedValueInterface $label
     */
    public function removeLabel(TranslatedValueInterface $label);

    /**
     * @param string $language
     *
     * @return string
     */
    public function getLabel($language = 'en');

    /**
     * @return Collection
     */
    public function getLabels();

    /**
     * @return array
     */
    public function getDocumentRoles();

    /**
     * @param DocumentGroupRoleInterface $documentGroupRole
     */
    public function addDocumentRole(DocumentGroupRoleInterface $documentGroupRole);

    /**
     * @param string $type
     * @param string $id
     * @param string $role
     *
     * @return DocumentGroupRoleInterface|null
     */
    public function getDocumentRoleByTypeAndIdAndRole($type, $id, $role);

    /**
     * @param string $type
     * @param string $id
     * @param string $role
     *
     * @return boolean
     */
    public function hasDocumentRoleByTypeAndIdAndRole($type, $id, $role);

}
