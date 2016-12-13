<?php

namespace OpenOrchestra\Backoffice\Model;

use FOS\UserBundle\Model\GroupInterface as BaseGroupInterface;
use Doctrine\Common\Collections\Collection;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowProfileCollectionInterface;

/**
 * Interface GroupInterface
 */
interface GroupInterface extends BaseGroupInterface
{
    const ENTITY_TYPE = 'Group';

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
     * @param WorkflowProfileCollectionInterface $workflowProfileCollection
     */
    public function addWorkflowProfileCollection($entityType, WorkflowProfileCollectionInterface $workflowProfileCollection);

    /**
     * @param Collection $workflowProfileCollections
     */
    public function setWorkflowProfileCollections(Collection $workflowProfileCollections);

    /**
     * @return Collection
     */
    public function getWorkflowProfileCollections();

    /**
     * @param string $entityType
     *
     * @return WorkflowProfileCollectionInterface|null
     */
    public function getWorkflowProfileCollection($entityType);

    /**
     * @param PerimeterInterface $perimeter
     */
    public function addPerimeter(PerimeterInterface $perimeter);

    /**
     * @param Collection $perimeters
     */
    public function setPerimeters(Collection $perimeters);

    /**
     * @param string $perimeterType
     *
     * @return array
     */
    public function getPerimeter($perimeterType);

    /**
     * @return Collection
     */
    public function getPerimeters();
}
