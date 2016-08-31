<?php

namespace OpenOrchestra\Backoffice\Tests\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\TranslatedValueContainerInterface;
use Phake;
use OpenOrchestra\Backoffice\EventListener\TranslateValueInitializerListener;

/**
 * Class TranslateValueInitializerListenerTest
 *
 * @deprecated will be removed in 2.0
 */
class TranslateValueInitializerListenerTest extends AbstractBaseTestCase
{
    /**
     * @var TranslateValueInitializerListener
     */
    protected $listener;

    /**
     * @var ArrayCollection
     */
    protected $names;

    protected $form;
    protected $event;
    protected $object;
    protected $fields;
    protected $fakeClass;
    protected $translatedValueDefaultValueInitializer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->fakeClass = 'OpenOrchestra\Backoffice\Tests\EventListener\FakeClassWithTranslatedProperties';
        $this->translatedValueDefaultValueInitializer = Phake::mock('OpenOrchestra\Backoffice\Initializer\TranslatedValueDefaultValueInitializer');
        $this->names = new ArrayCollection();
        $this->fields = new ArrayCollection();
        $this->object = Phake::mock('OpenOrchestra\Backoffice\Tests\EventListener\FakeClassWithTranslatedProperties');
        Phake::when($this->object)->getNames()->thenReturn($this->names);
        Phake::when($this->object)->getFields()->thenReturn($this->fields);

        $this->form = Phake::mock('Symfony\Component\Form\Form');
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getData()->thenReturn($this->object);
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $this->listener = new TranslateValueInitializerListener($this->translatedValueDefaultValueInitializer, $this->fakeClass);
    }

    /**
     * Test if method is present
     */
    public function testCallable()
    {
        $this->assertTrue(is_callable(array($this->listener, 'preSetData')));
        $this->assertTrue(is_callable(array($this->listener, 'preSubmitFieldType')));
    }

    /**
     * test pre set data
     */
    public function testPreSetDataWithNoPreviousData()
    {
        $translatedProperties = array('getNames');

        Phake::when($this->object)->getTranslatedProperties()->thenReturn($translatedProperties);

        $this->listener->preSetData($this->event);

        Phake::verify($this->translatedValueDefaultValueInitializer)->generate(Phake::anyParameters());
    }

    /**
     * @param array $translatedProperties
     * @param int   $callTimes
     *
     * @dataProvider provideProperties
     */
    public function testPreSetDataWithDifferentProperties($translatedProperties, $callTimes)
    {
        Phake::when($this->object)->getTranslatedProperties()->thenReturn($translatedProperties);

        $this->listener->preSetData($this->event);

        Phake::verify($this->translatedValueDefaultValueInitializer, Phake::times($callTimes))->generate(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideProperties()
    {
        return array(
            array(array(), 0),
            array(array('getNames'), 1),
            array(array('getNames', 'getFields'), 2),
        );
    }

    /**
     * Test pre submit with no data
     */
    public function testPreSubmitWithNoData()
    {
        Phake::when($this->form)->getData()->thenReturn(null);

        $this->listener->preSubmitFieldType($this->event);

        Phake::verify($this->form)->setData(Phake::anyParameters());
    }

    /**
     * Test pre submit with data
     *
     * @dataProvider provideClass
     */
    public function testPreSubmitWithData($class)
    {
        Phake::when($this->form)->getData()->thenReturn(Phake::mock($class));

        $this->listener->preSubmitFieldType($this->event);

        Phake::verify($this->form, Phake::never())->setData(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideClass()
    {
        return array(
            array('stdClass'),
            array('OpenOrchestra\ModelInterface\Model\FieldTypeInterface'),
        );
    }
}

class FakeClassWithTranslatedProperties implements TranslatedValueContainerInterface
{
    public function getNames()
    {
        return new ArrayCollection();
    }

    public function getFields()
    {
        return new ArrayCollection();
    }

    public function getTranslatedProperties()
    {
        return array(
            "getNames"
        );
    }
}