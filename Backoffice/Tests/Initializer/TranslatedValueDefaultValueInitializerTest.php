<?php

namespace OpenOrchestra\Backoffice\Tests\Initializer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\Backoffice\Initializer\TranslatedValueDefaultValueInitializer;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

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
            $this->assertTrue($properties->exists(function ($key, $element) use ($defaultLanguage) {
                return $defaultLanguage == $element->getLanguage();
            }));
        }
    }

    /**
     * @return array
     */
    public function provideProperties()
    {
        $frValue = $this->createMockTranslatedValue('fr');
        $enValue = $this->createMockTranslatedValue('en');
        $deValue = $this->createMockTranslatedValue('de');

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

    /**
     * @param string $language
     *
     * @return mixed
     */
    protected function createMockTranslatedValue($language)
    {
        $translatedValue = \Phake::mock('OpenOrchestra\ModelInterface\Model\TranslatedValueInterface');
        Phake::when($translatedValue)->getLanguage()->thenReturn($language);

        return $translatedValue;
    }
}
