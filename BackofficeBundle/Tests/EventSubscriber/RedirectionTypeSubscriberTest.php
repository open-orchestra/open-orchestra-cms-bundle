<?php

namespace OpenOrchestra\BackofficeBundle\Tests\EventSubscriber;

use OpenOrchestra\BackofficeBundle\EventSubscriber\RedirectionTypeSubscriber;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use Symfony\Component\Form\FormEvents;

/**
 * Test RedirectionTypeSubscriberTest
 */
class RedirectionTypeSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var RedirectionTypeSubscriber
     */
    protected $subscriber;

    protected $event;
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = Phake::mock('Symfony\Component\Form\FormInterface');
        Phake::when($this->form)->get(Phake::anyParameters())->thenReturn($this->form);
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $this->subscriber = new RedirectionTypeSubscriber();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * Test event subscribed
     */
    public function testGetSubscribedEvent()
    {
        $this->assertArrayHasKey(FormEvents::POST_SUBMIT, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test no interaction
     */
    public function testPostSubmitWithNoData()
    {
        $this->subscriber->postSubmit($this->event);

        Phake::verify($this->form, Phake::never())->get(Phake::anyParameters());
    }

    /**
     * @param string $siteId
     * @param array  $options
     * @param int    $callNumber
     *
     * @dataProvider provideSiteIdOptionsAndCallNumber
     */
    public function testPostSubmitWithDatas($siteId, array $options, $callNumber)
    {
        $data = Phake::mock('OpenOrchestra\ModelInterface\Model\RedirectionInterface');
        Phake::when($data)->getSiteId()->thenReturn($siteId);
        Phake::when($this->event)->getData()->thenReturn($data);

        $formConfig = Phake::mock('Symfony\Component\Form\FormConfigInterface');
        Phake::when($formConfig)->getOption(Phake::anyParameters())->thenReturn($options);
        Phake::when($this->form)->getConfig()->thenReturn($formConfig);

        $this->subscriber->postSubmit($this->event);

        if ($callNumber > 0) {
            Phake::verify($data)->setSiteName($options[$siteId]);
        } else {
            Phake::verify($data, Phake::never())->setSiteName(Phake::anyParameters());
        }
    }

    /**
     * @return array
     */
    public function provideSiteIdOptionsAndCallNumber()
    {
        return array(
            array('1', array('1' => 'foo'), 1),
            array('2', array('1' => 'foo'), 0),
        );
    }
}
