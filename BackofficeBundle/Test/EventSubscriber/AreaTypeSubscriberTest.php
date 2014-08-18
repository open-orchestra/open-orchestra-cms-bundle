<?php

namespace PHPOrchestra\BackofficeBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use PHPOrchestra\BackofficeBundle\EventSubscriber\AreaTypeSubscriber;
use Symfony\Component\Form\FormEvents;

/**
 * Class AreaTypeSubscriberTest
 */
class AreaTypeSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AreaTypeSubscriber
     */
    protected $subscriber;

    protected $event;
    protected $form;
    protected $node;
    protected $area;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->node = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        $this->area = Phake::mock('PHPOrchestra\ModelBundle\Model\AreaInterface');

        $this->form = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->form)->add(Phake::anyParameters())->thenReturn($this->form);
        Phake::when($this->form)->getData()->thenReturn($this->area);

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $this->subscriber = new AreaTypeSubscriber($this->node);
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
    public function testWithNoNewBlockAndNoNewArea()
    {
        $newBlocks = array();
        Phake::when($this->event)->getData()->thenReturn($newBlocks);

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->node, Phake::never())->addBlock(Phake::anyParameters());
        Phake::verify($this->area, Phake::never())->addBlock(Phake::anyParameters());
    }

    /**
     * Test add one block
     */
    public function testWithOneNewBlockAddition()
    {
        $blockIndex = 1;
        $newBlocks = array('Sample');
        Phake::when($this->event)->getData()->thenReturn(array('newBlocks' => $newBlocks));

        Phake::when($this->node)->getBlockIndex(Phake::anyParameters())->thenReturn($blockIndex);

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->node)->addBlock(Phake::anyParameters());
        Phake::verify($this->node)->getBlockIndex(Phake::anyParameters());
        Phake::verify($this->area)->addBlock(array(
            'nodeId' => 0,
            'blockId' => $blockIndex
        ));
        Phake::verify($this->area, Phake::never())->addSubArea(Phake::anyParameters());
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

        Phake::verify($this->node, Phake::never())->addBlock(Phake::anyParameters());
        Phake::verify($this->node, Phake::never())->getBlockIndex(Phake::anyParameters());
        Phake::verify($this->area, Phake::never())->addBlock(Phake::anyParameters());
        Phake::verify($this->area, Phake::times(count($newAreas)))->addSubArea(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideNewAreas()
    {
        return array(
            array(array('Sample')),
            array(array('Sample', 'Test')),
            array(array('Sample', 'new_area')),
        );
    }

    /**
     * @param int   $areaNumber
     * @param array $blockArray
     *
     * @dataProvider provideAreaOrBlockNumber
     */
    public function testPreSetData($areaNumber, $blockArray)
    {
        Phake::when($this->event)->getData()->thenReturn($this->area);

        $areaArrayCollection = Phake::mock('Doctrine\Common\Collections\ArrayCollection');
        Phake::when($areaArrayCollection)->count()->thenReturn($areaNumber);
        Phake::when($this->area)->getSubAreas()->thenReturn($areaArrayCollection);
        Phake::when($this->area)->getBlocks()->thenReturn($blockArray);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->form, Phake::times(1 - $areaNumber))->add('newBlocks', 'collection', array(
            'type' => 'orchestra_block',
            'allow_add' => true,
            'mapped' => false,
            'attr' => array(
                'data-prototype-label-add' => 'Ajout',
                'data-prototype-label-remove' => 'Suppression',
            )
        ));
        Phake::verify($this->form, Phake::times(1 - count($blockArray)))->add('newAreas', 'collection', array(
            'type' => 'text',
            'allow_add' => true,
            'mapped' => false,
            'attr' => array(
                'data-prototype-label-add' => 'Ajout',
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
            array(0, array()),
            array(0, array('blocks')),
            array(1, array()),
            array(1, array('blocks')),
        );
    }
}
