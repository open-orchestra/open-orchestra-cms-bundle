<?php

namespace OpenOrchestra\GroupBundle\Tests\Document;

use OpenOrchestra\GroupBundle\Document\Group;
use \Phake;

/**
 * Class GroupTest
 */
class GroupTest extends \PHPUnit_Framework_TestCase
{
    protected $labels;
    protected $label;
    protected $group;
    protected $site;

    /**
     * set Up
     */
    public function setUp()
    {
        $this->group = new Group();
        $this->site = Phake::mock('OpenOrchestra\ModelInterface\Model\ReadSiteInterface');
        $this->labels = Phake::mock('Doctrine\Common\Collections\ArrayCollection');
        $this->label = Phake::mock('OpenOrchestra\ModelInterface\Model\TranslatedValueInterface');
        Phake::when($this->label)->getLanguage()->thenReturn('en');
    }

    /**
     * test site
     */
    public function testSite(){
        $this->assertSame(null, $this->group->getSite());
        $this->group->setSite($this->site);
        $this->isTrue($this->site == $this->group->getSite());
    }

    /**
     * test Labels
     */
    public function testLabels()
    {
        $this->isTrue($this->labels == $this->group->getLabels());
        $this->group->addLabel($this->label);
        $this->isTrue($this->label == $this->group->getLabel());
        $this->group->removeLabel($this->label);
        $clone = clone $this->group;
        $this->isTrue($clone == new Group());
    }

    /**
     * test getTranslatedProperties
     */
    public function testGetTranslatedProperties()
    {
        $this->assertSame(['getLabels'], $this->group->getTranslatedProperties());
    }


}
