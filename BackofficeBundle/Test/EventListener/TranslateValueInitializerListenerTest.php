<?php

namespace PHPOrchestra\BackofficeBundle\Test\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use PHPOrchestra\BackofficeBundle\EventListener\TranslateValueInitializerListener;
use PHPOrchestra\ModelBundle\Document\TranslatedValue;

/**
 * Class TranslateValueInitializerListenerTest
 */
class TranslateValueInitializerListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TranslateValueInitializerListener
     */
    protected $listener;

    /**
     * @var ArrayCollection
     */
    protected $names;

    protected $fields;
    protected $event;
    protected $object;
    protected $defaultLanguages;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->defaultLanguages = array('en', 'fr', 'es', 'de');
        $this->names = new ArrayCollection();
        $this->fields = new ArrayCollection();
        $this->object = Phake::mock('PHPOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($this->object)->getNames()->thenReturn($this->names);
        Phake::when($this->object)->getFields()->thenReturn($this->fields);

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getData()->thenReturn($this->object);

        $this->listener = new TranslateValueInitializerListener($this->defaultLanguages);
    }

    /**
     * Test if method is present
     */
    public function testCallable()
    {
        $this->assertTrue(is_callable(array($this->listener, 'preSetData')));
    }

    /**
     * @param array $defaultLanguages
     *
     * @dataProvider provideDefaultLanguages
     */
    public function testPreSetDataWithNoPreviousData($defaultLanguages)
    {
        $translatedProperties = array('getNames');

        Phake::when($this->object)->getTranslatedProperties()->thenReturn($translatedProperties);

        $listener = new TranslateValueInitializerListener($defaultLanguages);
        $listener->preSetData($this->event);

        $this->assertCount(count($defaultLanguages), $this->names);
        foreach ($defaultLanguages as $key => $language) {
            $translatedValue = $this->names->get($key);
            $this->assertInstanceOf('PHPOrchestra\ModelInterface\Model\TranslatedValueInterface', $translatedValue);
            $this->assertSame($language, $translatedValue->getLanguage());
            $this->assertNull($translatedValue->getValue());
        }
    }

    /**
     * @return array
     */
    public function provideDefaultLanguages()
    {
        return array(
            array(array('en')),
            array(array('en', 'fr')),
            array(array('en', 'fr', 'es')),
            array(array('en', 'fr', 'es', 'de')),
        );
    }

    /**
     * @param array $defaultLanguages
     *
     * @dataProvider provideDefaultLanguages
     */
    public function testPreSetDataWithPreviousData($defaultLanguages)
    {
        $dummyValue = 'dummyValue';
        foreach ($defaultLanguages as $language) {
            $translatedValue = new TranslatedValue();
            $translatedValue->setLanguage($language);
            $translatedValue->setValue($dummyValue);
            $this->names->add($translatedValue);
        }

        $translatedProperties = array('getNames');

        Phake::when($this->object)->getTranslatedProperties()->thenReturn($translatedProperties);

        $listener = new TranslateValueInitializerListener($defaultLanguages);
        $listener->preSetData($this->event);

        $this->assertCount(count($defaultLanguages), $this->names);
        foreach ($defaultLanguages as $key => $language) {
            $translatedValue = $this->names->get($key);
            $this->assertInstanceOf('PHPOrchestra\ModelInterface\Model\TranslatedValueInterface', $translatedValue);
            $this->assertSame($language, $translatedValue->getLanguage());
            $this->assertSame($dummyValue, $translatedValue->getValue());
        }
    }

    /**
     * @param array $defaultLanguages
     * @param array $translatedProperties
     *
     * @dataProvider provideDefaultLanguagesAndProperties
     */
    public function testPreSetDataWithDifferentProperties($defaultLanguages, $translatedProperties)
    {
        Phake::when($this->object)->getTranslatedProperties()->thenReturn($translatedProperties);

        $listener = new TranslateValueInitializerListener($defaultLanguages);
        $listener->preSetData($this->event);

        $this->assertEquals(count($defaultLanguages) * count($translatedProperties), $this->names->count() + $this->fields->count());
    }

    /**
     * @return array
     */
    public function provideDefaultLanguagesAndProperties()
    {
        return array(
            array(array('en'), array('getNames')),
            array(array('en', 'fr'), array('getFields')),
            array(array('en'), array('getNames', 'getFields')),
            array(array('en', 'fr'), array('getFields', 'getNames')),
        );
    }
}
