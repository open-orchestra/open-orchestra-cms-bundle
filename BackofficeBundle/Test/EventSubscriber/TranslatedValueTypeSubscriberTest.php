<?php

namespace OpenOrchestra\BackofficeBundle\Test\EventSubscriber;

use Phake;
use OpenOrchestra\BackofficeBundle\EventSubscriber\TranslatedValueTypeSubscriber;
use Symfony\Component\Form\FormEvents;

/**
 * Class TranslatedValueTypeSubscriberTest
 */
class TranslatedValueTypeSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TranslatedValueTypeSubscriber
     */
    protected $subscriber;

    protected $form;
    protected $data;
    protected $event;
    protected $builder;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->data = Phake::mock('OpenOrchestra\ModelInterface\Model\TranslatedValueInterface');

        $this->form = Phake::mock('Symfony\Component\Form\Form');
        Phake::when($this->form)->add(Phake::anyParameters())->thenReturn($this->form);

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);
        Phake::when($this->event)->getData()->thenReturn($this->data);

        $this->subscriber = new TranslatedValueTypeSubscriber();
    }

    /**
     * Test instance and event subscribed
     */
    public function testInstanceAndEventSubscribed()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
        $this->assertArrayHasKey(FormEvents::PRE_SET_DATA, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @param string $language
     *
     * @dataProvider provideLanguage
     */
    public function testPreSetData($language)
    {
        Phake::when($this->data)->getLanguage()->thenReturn($language);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->form)->add('value', 'text', array(
            'label' => $language,
            'attr' => array(
                'class' => 'generate-id-source',
            )
        ));
    }

    /**
     * @return array
     */
    public function provideLanguage()
    {
        return array(
            array('en'),
            array('fr'),
            array('de'),
            array('es'),
        );
    }
}
