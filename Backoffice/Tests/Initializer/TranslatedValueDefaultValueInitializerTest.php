<?php

namespace OpenOrchestra\Backoffice\Tests\Initializer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\Backoffice\Initializer\TranslatedValueDefaultValueInitializer;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelBundle\Document\TranslatedValue;

/**
 * Test TranslatedValueDefaultValueInitializerTest
 */
class TranslatedValueDefaultValueInitializerTest extends AbstractBaseTestCase
{
    /**
     * @var TranslatedValueDefaultValueInitializer
     */
    protected $translatedValueDefaultValueInitializer;

    protected $translatedValueClass;
    protected $defaultLanguages;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translatedValueClass = 'OpenOrchestra\ModelBundle\Document\TranslatedValue';
        $this->defaultLanguages = array('en', 'fr', 'es', 'de');

        $this->translatedValueDefaultValueInitializer = new TranslatedValueDefaultValueInitializer($this->defaultLanguages, $this->translatedValueClass);
    }

    /**
     * @param ArrayCollection $properties
     *
     * @dataProvider provideProperties
     */
    public function testGenerate(ArrayCollection $properties)
    {
        $this->translatedValueDefaultValueInitializer->generate($properties);

        $this->assertCount(4, $properties);
        foreach ($this->defaultLanguages as $defaultLanguage) {
            $this->assertTrue($properties->exists(function ($key, TranslatedValue $element) use ($defaultLanguage) {
                return $defaultLanguage == $element->getLanguage();
            }));
        }
    }

    /**
     * @return array
     */
    public function provideProperties()
    {
        $frValue = new TranslatedValue();
        $frValue->setLanguage('fr');
        $enValue = new TranslatedValue();
        $enValue->setLanguage('en');
        $deValue = new TranslatedValue();
        $deValue->setLanguage('de');

        $emptyProperties = new ArrayCollection();
        $oneLanguage = new ArrayCollection(array($frValue));
        $twoLanguages = new ArrayCollection(array($frValue, $enValue));
        $threeLanguages = new ArrayCollection(array($frValue, $enValue, $deValue));

        return array(
            array($emptyProperties),
            array($oneLanguage),
            array($twoLanguages),
            array($threeLanguages),
        );
    }
}
