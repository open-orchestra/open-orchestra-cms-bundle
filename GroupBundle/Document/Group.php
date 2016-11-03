<?php

namespace OpenOrchestra\GroupBundle\Document;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\UserBundle\Document\Group as BaseGroup;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\Mapping\Annotations as ORCHESTRA;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ODM\Document(
 *  collection="users_group",
 *  repositoryClass="OpenOrchestra\GroupBundle\Repository\GroupRepository"
 * )
 */
class Group extends BaseGroup implements GroupInterface
{
    /**
     * @ODM\ReferenceOne(targetDocument="OpenOrchestra\ModelInterface\Model\ReadSiteInterface")
     */
    protected $site;

    /**
     * @var Collection $labels
     *
     * @ODM\Field(type="hash")
     * @ORCHESTRA\Search(key="label", type="multiLanguages")
     */
    protected $labels;

    /**
     * @var Collection
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\WorkflowFunction\Model\WorkflowProfileCollectionInterface", strategy="set")
     */
    protected $workflowProfiles;

    /**
     * @var Collection $perimeters
     *
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\Backoffice\Model\PerimeterInterface", strategy="set")
     */
    protected $perimeters;

    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->labels = array();
        $this->roles = array();
        $this->perimeters = new ArrayCollection();
    }

    /**
     * Clone the element
     */
    public function __clone()
    {
        $this->id = null;
        $this->perimeters = new ArrayCollection();

        $this->setName($this->cloneLabel($this->name));

        foreach ($this->getLabels() as $language => $label) {
            $this->addLabel($language, $this->cloneLabel($label));
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
     * @param string $label
     *
     * @return string
     */
    protected function cloneLabel($label)
    {
        $patternNameVersion = '/.*_([0-9]+$)/';
        if (0 !== preg_match_all($patternNameVersion, $label, $matches)) {
            $version = (int) $matches[1][0] + 1;
            return preg_replace('/[0-9]+$/', $version, $label);
        }

        return $label . '_2';
    }
}
