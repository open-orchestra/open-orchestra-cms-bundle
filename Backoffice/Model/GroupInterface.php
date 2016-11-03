<?php

namespace OpenOrchestra\Backoffice\Model;

use FOS\UserBundle\Model\GroupInterface as BaseGroupInterface;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;

/**
 * Interface GroupInterface
 */
interface GroupInterface extends BaseGroupInterface
{
    // TO REMOVE
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
}
