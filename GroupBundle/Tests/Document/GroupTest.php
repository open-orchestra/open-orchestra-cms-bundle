<?php

namespace OpenOrchestra\GroupBundle\Tests\Document;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\GroupBundle\Document\Group;
use Phake;

/**
 * Class GroupTest
 */
class GroupTest extends AbstractBaseTestCase
{
    /**
     * @var Group
     */
    protected $group;

    /**
     * set Up
     */
    public function setUp()
    {
        $this->group = new Group();
    }

    /**
     * test site
     */
    public function testSite()
    {
        $this->assertNull($this->group->getSite());

        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        $this->group->setSite($site);
        $this->assertSame($site, $this->group->getSite());
    }

    /**
     * @param string $language
     * @param string $value
     *
     * @dataProvider provideLabelLanguageAndValue
     */
    public function testLabels($language, $value)
    {
        $this->group->addLabel($language, $value);

        $this->assertSame($value, $this->group->getLabel($language));
    }

    /**
     * @return array
     */
    public function provideLabelLanguageAndValue()
    {
        return array(
            array('en', 'foo'),
            array('fr', 'bar'),
        );
    }


    /**
     * @param string $name
     * @param string $expectedName
     * @param array  $labels
     * @param array  $expectedLabels
     *
     * @dataProvider provideLabelLanguageAndName
     */
    public function testClone($name, $expectedName, array $labels, array $expectedLabels)
    {
        $this->group->setName($name);
        $this->group->setLabels($labels);

        $cloneGroup = clone $this->group;

        $this->assertNull($cloneGroup->getId());
        $this->assertEquals(0, strpos($cloneGroup->getName(), $expectedName));
        $this->assertSame($cloneGroup->getLabels(), $expectedLabels);
    }

    /**
     * @return array
     */
    public function provideLabelLanguageAndName()
    {
        return array(
            array('name', 'name_', array('en' => 'labelen'), array('en' => 'labelen')),
            array('name_2', 'name_2_', array('en' => 'labelen_2'), array('en' => 'labelen_2')),
            array('name_2_55', 'name_2_55_', array('en' => 'labelen2_5'), array('en' => 'labelen2_5')),
        );
    }
}
