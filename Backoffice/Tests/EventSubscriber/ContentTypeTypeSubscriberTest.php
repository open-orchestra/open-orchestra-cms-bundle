<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\EventSubscriber\ContentTypeTypeSubscriber;
use Symfony\Component\Form\FormEvents;

/**
 * Class ContentTypeTypeSubscriberTest
 */
class ContentTypeTypeSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var ContentTypeTypeSubscriber
     */
    protected $subscriber;

    protected $event;
    protected $form;
    protected $data;
    protected $formConfig;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = Phake::mock('Symfony\Component\Form\FormInterface');
        $this->data = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        $child = Phake::mock('Symfony\Component\Form\FormInterface');;
        $resolvedFormType = Phake::mock('Symfony\Component\Form\ResolvedFormTypeInterface');
        $this->formConfig = Phake::mock('Symfony\Component\Form\FormConfigInterface');

        Phake::when($resolvedFormType)->getName()->thenReturn('text');
        Phake::when($this->formConfig)->getType()->thenReturn($resolvedFormType);
        Phake::when($child)->getConfig()->thenReturn($this->formConfig);
        Phake::when($child)->getName()->thenReturn('contentTypeId');
        Phake::when($this->event)->getForm()->thenReturn($this->form);
        Phake::when($this->event)->getData()->thenReturn($this->data);
        Phake::when($this->event)->getData()->thenReturn($this->data);
        Phake::when($this->form)->get('contentTypeId')->thenReturn($child);

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
     * @param array $expectedOptions
     *
     * @dataProvider generateOptions
     */
    public function testOnPreSetData($contentTypeId, array $options, $nbrCall, array $expectedOptions)
    {
        Phake::when($this->data)->getContentTypeId()->thenReturn($contentTypeId);
        Phake::when($this->formConfig)->getOptions()->thenReturn($options);

        $this->subscriber->onPreSetData($this->event);

        Phake::verify($this->form, Phake::times($nbrCall))->add('contentTypeId', 'text', $expectedOptions);
    }

    /**
     * @return array
     */
    public function generateOptions()
    {
        return array(
            array(null,
                array(
                    'label' => 'open_orchestra_backoffice.form.content_type.content_type_id',
                    'attr' => array(
                        'class' => 'generate-id-dest',
                        'help_text' => 'open_orchestra_backoffice.form.allowed_characters.helper',
                    )
                ),
                0,
                array()
            ),
            array('fakeContentId',
                array(
                    'label' => 'open_orchestra_backoffice.form.content_type.content_type_id',
                    'attr' => array(
                        'class' => 'generate-id-dest',
                        'help_text' => 'open_orchestra_backoffice.form.allowed_characters.helper',
                    )
                ),
                1,
                array(
                    'label' => 'open_orchestra_backoffice.form.content_type.content_type_id',
                    'attr' => array(
                        'class' => 'generate-id-dest',
                        'help_text' => 'open_orchestra_backoffice.form.allowed_characters.helper',
                    ),
                    'disabled' => true
                )
            )
        );
    }
}
