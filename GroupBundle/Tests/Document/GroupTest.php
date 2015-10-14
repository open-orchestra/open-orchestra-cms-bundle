<?php

namespace OpenOrchestra\GroupBundle\Tests\Document;

use OpenOrchestra\GroupBundle\Document\Group;
use Phake;

/**
 * Class GroupTest
 */
class GroupTest extends \PHPUnit_Framework_TestCase
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
        $label = Phake::mock('OpenOrchestra\ModelInterface\Model\TranslatedValueInterface');
        Phake::when($label)->getLanguage()->thenReturn($language);
        Phake::when($label)->getValue()->thenReturn($value);
        $this->group->addLabel($label);

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
     * test getTranslatedProperties
     */
    public function testGetTranslatedProperties()
    {
        $this->assertSame(array('getLabels'), $this->group->getTranslatedProperties());
    }
}
