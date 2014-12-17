<?php

namespace PHPOrchestra\BackofficeBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\BackofficeBundle\EventSubscriber\AreaCollectionSubscriber;
use Symfony\Component\Form\FormEvents;

/**
 * Class AreaCollectionSubscriberTest
 */
class AreaCollectionSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AreaCollectionSubscriber
     */
    protected $subscriber;

    protected $form;
    protected $event;
    protected $areaContainer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->areaContainer = Phake::mock('PHPOrchestra\ModelInterface\Model\AreaContainerInterface');

        $this->form = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->form)->add(Phake::anyParameters())->thenReturn($this->form);
        Phake::when($this->form)->getData()->thenReturn($this->areaContainer);

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $this->subscriber = new AreaCollectionSubscriber();
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
        $this->assertArrayHasKey(FormEvents::PRE_SUBMIT, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(FormEvents::PRE_SET_DATA, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test with no addition
     */
    public function testWithNoNewArea()
    {
        $newBlocks = array();
        Phake::when($this->event)->getData()->thenReturn($newBlocks);

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->areaContainer, Phake::never())->addBlock(Phake::anyParameters());
    }

    /**
     * @param array $newAreas
     *
     * @dataProvider provideNewAreas
     */
    public function testWithMultipleAreaAddition($newAreas)
    {
        Phake::when($this->event)->getData()->thenReturn(array('newAreas' => $newAreas));

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->areaContainer, Phake::never())->addBlock(Phake::anyParameters());
        Phake::verify($this->areaContainer, Phake::times(count($newAreas)))->addArea(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideNewAreas()
    {
        return array(
            array(array('sample')),
            array(array('sample', 'Test')),
            array(array('sample', 'new_area')),
        );
    }

    /**
     * @param array $blockArray
     *
     * @dataProvider provideAreaOrBlockNumber
     */
    public function testPreSetData($blockArray)
    {
        Phake::when($this->event)->getData()->thenReturn($this->areaContainer);

        Phake::when($this->areaContainer)->getBlocks()->thenReturn($blockArray);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->form, Phake::times(1 - count($blockArray)))->add('newAreas', 'collection', array(
            'type' => 'text',
            'allow_add' => true,
            'mapped' => false,
            'label' => 'php_orchestra_backoffice.form.area.new_areas',
            'attr' => array(
                'data-prototype-label-add' => 'Ajout',
                'data-prototype-label-new' => 'Nouveau',
                'data-prototype-label-remove' => 'Suppression',
            )
        ));
    }

    /**
     * @return array
     */
    public function provideAreaOrBlockNumber()
    {
        return array(
            array(array()),
            array(array('blocks')),
        );
    }

    /**
     * test with new node
     */
    public function testPreSetDataWithNewNode()
    {
        $nodeInterface = Phake::mock('PHPOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($nodeInterface)->getId()->thenReturn(null);
        Phake::when($this->event)->getData()->thenReturn($nodeInterface);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->form, Phake::never())->add(Phake::anyParameters());
    }
}
