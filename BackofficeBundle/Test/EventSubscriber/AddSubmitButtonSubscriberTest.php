<?php

namespace PHPOrchestra\BackofficeBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use PHPOrchestra\BackofficeBundle\EventSubscriber\BlockTypeSubscriber;
use Symfony\Component\Form\FormEvents;
use PHPOrchestra\ModelInterface\Model\StatusInterface;

/**
 * Class AddSubmitButtonSubscriberTest
 */
class AddSubmitButtonSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BlockTypeSubscriber
     */
    protected $subscriber;

    protected $status;
    protected $event;
    protected $form;
    protected $data;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->form)->add(Phake::anyParameters())->thenReturn($this->form);

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $this->subscriber = new AddSubmitButtonSubscriber();
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
        $this->assertArrayHasKey(FormEvents::POST_SET_DATA, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test add a submit button
     *
     * @param StatusInterface $status
     * @param array           $expectedParameters
     *
     * @dataProvider provideStatus
     */
    public function testPostSetData(StatusInterface $status = null, $expectedParameters)
    {
        $data = Phake::mock('PHPOrchestra\ModelInterface\Model\StatusableInterface');
        Phake::when($data)->getStatus()->thenReturn($status);
        Phake::when($this->event)->getData()->thenReturn($data);

        $this->subscriber->postSetData($this->event);

        Phake::verify($this->form)->add('submit', 'submit', $expectedParameters);
    }

    /**
     * @return array
     */
    public function provideStatus()
    {
        $status0 = Phake::mock('PHPOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status0)->isPublished()->thenReturn(true);

        $status1 = Phake::mock('PHPOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status1)->isPublished()->thenReturn(false);

        return array(
            array($status0, array('label' => 'php_orchestra_base.form.submit', 'attr' => array('class' => 'disabled'))),
            array($status1, array('label' => 'php_orchestra_base.form.submit')),
            array(null, array('label' => 'php_orchestra_base.form.submit')),
        );
    }
}
