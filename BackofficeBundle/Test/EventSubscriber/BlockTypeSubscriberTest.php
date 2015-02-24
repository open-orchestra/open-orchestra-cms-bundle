<?php

namespace OpenOrchestra\BackofficeBundle\Test\EventSubscriber;

use Phake;
use OpenOrchestra\BackofficeBundle\EventSubscriber\BlockTypeSubscriber;
use Symfony\Component\Form\FormEvents;

/**
 * Class BlockTypeSubscriberTest
 */
class BlockTypeSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BlockTypeSubscriber
     */
    protected $subscriber;

    protected $form;
    protected $event;
    protected $block;
    protected $formConfig;
    protected $formFactory;
    protected $fixedParams;
    protected $generateFormManager;
    protected $generateFormInterface;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');

        $this->formConfig = Phake::mock('Symfony\Component\Form\FormConfigInterface');
        Phake::when($this->formConfig)->getModelTransformers()->thenReturn(array());
        Phake::when($this->formConfig)->getViewTransformers()->thenReturn(array());
        $this->form = Phake::mock('Symfony\Component\Form\Form');
        Phake::when($this->form)->add(Phake::anyParameters())->thenReturn($this->form);
        Phake::when($this->form)->get(Phake::anyParameters())->thenReturn($this->form);
        Phake::when($this->form)->getConfig()->thenReturn($this->formConfig);
        Phake::when($this->form)->all()->thenReturn(array($this->form));


        $this->formFactory = Phake::mock('Symfony\Component\Form\FormFactory');
        Phake::when($this->formFactory)->create(Phake::anyParameters())->thenReturn($this->form);
        $this->generateFormInterface = Phake::mock('OpenOrchestra\Backoffice\GenerateForm\GenerateFormInterface');

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $this->generateFormManager = Phake::mock('OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager');
        Phake::when($this->generateFormManager)->createForm(Phake::anyParameters())->thenReturn($this->generateFormInterface);

        $this->fixedParams = array('component', 'label', 'class', 'id');

        $this->subscriber = new BlockTypeSubscriber($this->generateFormManager, $this->fixedParams, $this->formFactory);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * Test subscribed events
     */
    public function testEventSubscribed()
    {
        $this->assertArrayHasKey(FormEvents::PRE_SET_DATA, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(FormEvents::PRE_SUBMIT, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test with no data
     */
    public function testPreSetDataWithNoAttributes()
    {
        Phake::when($this->event)->getForm()->thenReturn($this->form);
        Phake::when($this->event)->getData()->thenReturn($this->block);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->generateFormManager)->createForm($this->block);
        Phake::verify($this->form)->add(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideKeyAndData()
    {
        return array(
            array('test', array('tentative' => 'tentative', 'test')),
            array('key', array('value' => 'tentative', 'test', 'other' => 4)),
        );
    }
}
