<?php

namespace OpenOrchestra\Backoffice\Model;

use FOS\UserBundle\Model\GroupInterface as BaseGroupInterface;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\WorkflowFunction\Model\WorkflowProfileCollectionInterface;

/**
 * Interface GroupInterface
 */
interface GroupInterface extends BaseGroupInterface
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
     * @param string                             $entityType
     * @param WorkflowProfileCollectionInterface $profileCollection
     */
    public function addWorkflowProfileCollection($entityType, WorkflowProfileCollectionInterface $profileCollection);

    /**
     * @param string             $perimeterType
     * @param PerimeterInterface $perimeter
     */
    public function addPerimeter($perimeterType, PerimeterInterface $perimeter);
}
