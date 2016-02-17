<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\AreaFlexRowSubscriber;
use OpenOrchestra\Pagination\Tests\AbstractBaseTestCase;
use Symfony\Component\Form\FormEvents;
use Phake;

/**
 * Class AreaFlexRowSubscriberTest
 */
class AreaFlexRowSubscriberTest extends AbstractBaseTestCase
{
    protected $areaManager;
    protected $form;
    protected $event;
    /** @var AreaFlexRowSubscriber */
    protected $subscriber;
    protected $rowArea;

    /**
     * Set up the test
     */
    public function setUp()
    {

        $this->areaManager = Phake::mock('OpenOrchestra\BackofficeBundle\Manager\AreaFlexManager');
        Phake::when($this->areaManager)->initializeNewAreaColumn(Phake::anyParameters())->thenReturn(Phake::mock('OpenOrchestra\ModelInterface\Model\AreaFlexInterface'));

        $this->form = Phake::mock('Symfony\Component\Form\FormInterface');
        $this->rowArea = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaFlexInterface');
        Phake::when($this->form)->getData()->thenReturn($this->rowArea);

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $this->subscriber = new AreaFlexRowSubscriber($this->areaManager);
    }

    /**
     * Test Pre submit without column layout data
     */
    public function testPreSubmitWithoutColumnLayoutData()
    {
        Phake::when($this->event)->getData()->thenReturn(array('fakeData'));

        $this->subscriber->preSubmit($this->event);
        Phake::verify($this->areaManager, Phake::never())->addArea(Phake::anyParameters());
    }

    /**
     * @param string  $layout
     * @param integer $countLayout
     *
     * @dataProvider provideLayoutAndCountLayout
     */
    public function testPreSubmitWithColumnLayoutData($layout, $countLayout)
    {
        $data = array(
            "columnLayout" => array("layout" => $layout),
            "areaId" => "fakeAreaID"
        );
        Phake::when($this->event)->getData()->thenReturn($data);

        $this->subscriber->preSubmit($this->event);
        Phake::verify($this->rowArea, Phake::times($countLayout))->addArea(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideLayoutAndCountLayout()
    {
        return array(
            array('100%', 1),
            array('1,1,1', 3),
            array('1,10px,5', 3),
            array('1,10px', 2),
            array('1,auto', 2),
            array(' 1, auto', 2),
        );
    }

    /**
     * Test subscribed events
     */
    public function testEventSubscribed()
    {
        $this->assertArrayHasKey(FormEvents::PRE_SUBMIT, $this->subscriber->getSubscribedEvents());
    }
}
