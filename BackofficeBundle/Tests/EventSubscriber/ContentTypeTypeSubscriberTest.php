<?php

namespace OpenOrchestra\BackofficeBundle\Tests\EventSubscriber;

use Phake;
use OpenOrchestra\BackofficeBundle\EventSubscriber\ContentTypeTypeSubscriber;
use Symfony\Component\Form\FormEvents;

/**
 * Class ContentTypeTypeSubscriberTest
 */
class ContentTypeTypeSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentTypeTypeSubscriber
     */
    protected $subscriber;

    protected $event;
    protected $form;
    protected $data;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = Phake::mock('Symfony\Component\Form\Form');
        $this->data = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);
        Phake::when($this->event)->getData()->thenReturn($this->data);

        $this->subscriber = new ContentTypeTypeSubscriber();
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
    }

    /**
     * @param string $contentTypeId
     * @param array $options
     *
     * @dataProvider generateOptions
     */
    public function testOnPreSetData($contentTypeId, $options)
    {
        Phake::when($this->data)->getContentTypeId()->thenReturn($contentTypeId);

        $this->subscriber->onPreSetData($this->event);

        Phake::verify($this->form)->add('contentTypeId', 'text', $options);
    }

    /**
     * @return array
     */
    public function generateOptions()
    {
        return array(
            array(null, array(
                'label' => 'open_orchestra_backoffice.form.content_type.content_type_id',
                'attr' => array('class' => 'generate-id-dest')
            )),
            array('fakeContentId', array(
                'label' => 'open_orchestra_backoffice.form.content_type.content_type_id',
                'attr' => array('class' => 'generate-id-dest'),
                'disabled' => true
            ))
        );
    }
}
