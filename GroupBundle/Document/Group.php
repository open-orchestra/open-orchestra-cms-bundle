<?php

namespace OpenOrchestra\GroupBundle\Document;

use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\MongoTrait\SoftDeleteable;
use OpenOrchestra\UserBundle\Document\Group as BaseGroup;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\Backoffice\Model\PerimeterInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowProfileCollectionInterface;

/**
 * @ODM\Document(
 *  collection="users_group",
 *  repositoryClass="OpenOrchestra\GroupBundle\Repository\GroupRepository"
 * )
 */
class Group extends BaseGroup implements GroupInterface
{
    use SoftDeleteable;

    /**
     * @ODM\ReferenceOne(
     *  targetDocument="OpenOrchestra\ModelInterface\Model\ReadSiteInterface"
     * )
     */
    protected $site;

    /**
     * @var array $labels
     *
     * @ODM\Field(
     *  type="hash"
     * )
     *
     * @ORCHESTRA\Search(
     *  key="label",
     *  type="multiLanguages"
     * )
     */
    protected $labels;

    /**
     * @var Collection $workflowProfileCollections
     *
     * @ODM\EmbedMany(
     *  targetDocument="OpenOrchestra\ModelInterface\Model\WorkflowProfileCollectionInterface",
     *  strategy="set"
     * )
     */
    protected $workflowProfileCollections;

    /**
     * @var Collection $perimeters
     *
     * @ODM\EmbedMany(
     *  targetDocument="OpenOrchestra\Backoffice\Model\PerimeterInterface",
     *  strategy="set"
     * )
     */
    protected $perimeters;

    /**
     * Constructor
     */
    public function __construct($name = '', $roles = array())
    {
        parent::__construct($name, $roles);

        $this->initCollections();
        $this->labels = array();
    }

    /**
     * Clone the element
     */
    public function __clone()
    {
        $this->id = null;
        $this->initCollections();
        $this->setName($this->name . '_' . uniqid());

        foreach ($this->getLabels() as $language => $label) {
            $this->addLabel($language, $label);
        }
    }

    /**
     * @param ReadSiteInterface|null $site
     */
    public function setSite(ReadSiteInterface $site = null)
    {
        $this->site = $site;
    }

    /**
     * @return ReadSiteInterface|null
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param string $language
     * @param string $label
     */
    public function addLabel($language, $label)
    {
        if (is_string($language) && is_string($label)) {
            $this->labels[$language] = $label;
        }
    }

    /**
     * @param string $language
     */
    public function removeLabel($language)
    {
        if (is_string($language) && isset($this->labels[$language])) {
            unset($this->labels[$language]);
        }
    }

    /**
     * @param string $language
     *
     * @return string
     */
    public function getLabel($language)
    {
        if (isset($this->labels[$language])) {
            return $this->labels[$language];
        }

        return '';
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param array $labels
     */
    public function setLabels(array $labels)
    {
        foreach ($labels as $language => $label) {
            $this->addLabel($language, $label);
        }
    }

    /**
     * @param string                             $entityType
     * @param WorkflowProfileCollectionInterface $workflowProfileCollection
     */
    public function addWorkflowProfileCollection($entityType, WorkflowProfileCollectionInterface $workflowProfileCollection)
    {
        $this->workflowProfileCollections->set($entityType, $workflowProfileCollection);
    }

    /**
     * @param Collection $workflowProfileCollections
     */
    public function setWorkflowProfileCollections(Collection $workflowProfileCollections)
    {
        $this->workflowProfileCollections->clear();
        foreach ($workflowProfileCollections as $entityType => $workflowProfileCollection) {
            $this->workflowProfileCollections->set($entityType, $workflowProfileCollection);
        }
    }

    /**
     * @return Collection
     */
    public function getWorkflowProfileCollections()
    {
        return $this->workflowProfileCollections;
    }

    /**
     * @param string $entityType
     *
     * @return WorkflowProfileCollectionInterface|null
     */
    public function getWorkflowProfileCollection($entityType)
    {
        return $this->workflowProfileCollections->get($entityType);
    }

    /**
     * @param PerimeterInterface $perimeter
     */
    public function addPerimeter(PerimeterInterface $perimeter)
    {
        $this->perimeters->set($perimeter->getType(), $perimeter);
    }

    /**
     * @param Collection $perimeters
     */
    public function setPerimeters(Collection $perimeters)
    {
        $this->perimeters->clear();
        foreach ($perimeters as $perimeter) {
            $this->perimeters->set($perimeter->getType(), $perimeter);
        }
    }

    /**
     * @param string $perimeterType
     *
     * @return array
     */
    public function getPerimeter($perimeterType)
    {
        return $this->perimeters->get($perimeterType);
    }

    /**
     * @return Collection
     */
    public function getPerimeters()
    {
        return $this->perimeters;
    }

    /**
     * Initialize collections
     */
    protected function initCollections()
    {
        $this->workflowProfileCollections = new ArrayCollection();
        $this->perimeters = new ArrayCollection();
    }
}
