<?php

namespace PHPOrchestra\BackofficeBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\BaseBundle\EventSubscriber\AddSubmitButtonSubscriber;
use PHPOrchestra\BackofficeBundle\EventSubscriber\BlockCollectionSubscriber;
use Symfony\Component\Form\FormEvents;

/**
 * Class BlockCollectionSubscriberTest
 */
class BlockCollectionSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BlockCollectionSubscriber
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

        $this->subscriber = new BlockCollectionSubscriber($this->node);
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
        $newBlocks = array('sample');
        Phake::when($this->event)->getData()->thenReturn(array('newBlocks' => $newBlocks));

        Phake::when($this->node)->getBlockIndex(Phake::anyParameters())->thenReturn($blockIndex);

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->node)->addBlock(Phake::anyParameters());
        Phake::verify($this->node)->getBlockIndex(Phake::anyParameters());
        Phake::verify($this->area)->addBlock(array(
            'nodeId' => 0,
            'blockId' => $blockIndex
        ));
    }

    /**
     * @param int   $areaNumber
     *
     * @dataProvider provideAreaNumber
     */
    public function testPreSetData($areaNumber)
    {
        Phake::when($this->event)->getData()->thenReturn($this->area);

        $areaArrayCollection = Phake::mock('Doctrine\Common\Collections\ArrayCollection');
        Phake::when($areaArrayCollection)->count()->thenReturn($areaNumber);
        Phake::when($this->area)->getAreas()->thenReturn($areaArrayCollection);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->form, Phake::times(1 - $areaNumber))->add('newBlocks', 'collection', array(
            'type' => 'orchestra_block',
            'allow_add' => true,
            'mapped' => false,
            'attr' => array(
                'data-prototype-label-add' => 'Ajout',
                'data-prototype-label-new' => 'Nouveau',
                'data-prototype-label-remove' => 'Suppression',
            ),
            'label' => 'php_orchestra_backoffice.form.area.new_blocks'
        ));
        Phake::verify($this->form, Phake::times(1 - $areaNumber))->add('existingBlocks', 'collection', array(
            'type' => 'existing_block',
            'allow_add' => true,
            'mapped' => false,
            'attr' => array(
                'data-prototype-label-add' => 'Ajout',
                'data-prototype-label-new' => 'Nouveau',
                'data-prototype-label-remove' => 'Suppression',
            ),
            'label' => 'php_orchestra_backoffice.form.area.existing_blocks'
        ));
    }

    /**
     * @return array
     */
    public function provideAreaNumber()
    {
        return array(
            array(0),
            array(1),
        );
    }

    /**
     * Test one existing block addition
     */
    public function testWithOneExistingBlockAddition()
    {
        Phake::when($this->event)->getData()->thenReturn(array(
            'existingBlocks' => array(
                array('existingBlock' => 'fixture_full:0'),
                array('existingBlock' => 'root:1'),
            )
        ));

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->node, Phake::never())->addBlock(Phake::anyParameters());
        Phake::verify($this->node, Phake::never())->getBlockIndex(Phake::anyParameters());
        Phake::verify($this->area)->addBlock(array(
            'nodeId' => 'fixture_full',
            'blockId' => 0
        ));
        Phake::verify($this->area)->addBlock(array(
            'nodeId' => 'root',
            'blockId' => 1
        ));
    }
}
