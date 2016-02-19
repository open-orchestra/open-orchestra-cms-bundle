<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\AreaFlexRowSubscriber;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\Backoffice\Form\Type\Component\ColumnLayoutRowType;
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

        $this->areaManager = Phake::mock('OpenOrchestra\Backoffice\Manager\AreaFlexManager');
        Phake::when($this->areaManager)->initializeNewAreaColumn(Phake::anyParameters())->thenReturn(Phake::mock('OpenOrchestra\ModelInterface\Model\AreaFlexInterface'));

        $this->form = Phake::mock('Symfony\Component\Form\FormInterface');
        $this->rowArea = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaFlexInterface');
        $emptyCollection = Phake::mock('Doctrine\Common\Collections\Collection');
        Phake::when($this->rowArea)->getAreas()->thenReturn($emptyCollection);
        Phake::when($this->form)->getData()->thenReturn($this->rowArea);

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $this->subscriber = new AreaFlexRowSubscriber($this->areaManager);
    }

    /**
     * @param array  $layoutColumn
     * @param string $expectedValue
     *
     * @dataProvider provideLayoutColumn
     */
    public function testPreSetData(array $layoutColumn, $expectedValue)
    {
        $columnArea = array();
        foreach ($layoutColumn as $width) {
            $area = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaFlexInterface');
            Phake::when($area)->getWidth()->thenReturn($width);
            $columnArea[] = $area;
        }
        Phake::when($this->rowArea)->getAreas()->thenReturn($columnArea);
        Phake::when($this->rowArea)->getAreaId()->thenReturn("fakeId");
        Phake::when($this->event)->getData()->thenReturn($this->rowArea);

        $this->subscriber->preSetData($this->event);
        Phake::verify($this->form, Phake::times(1))->add('columnLayout', ColumnLayoutRowType::class, array(
            'label' => 'open_orchestra_backoffice.form.area_flex.column_layout.label',
            'mapped' => false,
            'attr' => array(
                'help_text' => 'open_orchestra_backoffice.form.area_flex.column_layout.helper',
            ),
            'data' => array('layout' => $expectedValue)
        ));
    }

    /**
     * @return array
     */
    public function provideLayoutColumn()
    {
        return array(
            array(array(), ""),
            array(array(1,2), "1,2"),
            array(array(5), "5"),
        );
    }

    /**
     * Test Pre submit without column layout data
     */
    public function testPreSubmitWithoutColumnLayoutData()
    {
        Phake::when($this->event)->getData()->thenReturn(array('fakeData'));

        $this->subscriber->preSubmit($this->event);
        Phake::verify($this->areaManager, Phake::never())->initializeNewAreaColumn(Phake::anyParameters());
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
        Phake::verify($this->areaManager, Phake::times($countLayout))->initializeNewAreaColumn(Phake::anyParameters());
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
     * @param integer $areaColumnCount
     * @param string  $layout
     * @param integer $layoutCount
     *
     * @dataProvider provideAreaCountAndLayout
     */
    public function testPreSubmitRemovedColumn($areaColumnCount, $layout, $layoutCount)
    {
        $columnArea = new ArrayCollection();
        for($i=0; $i < $areaColumnCount; $i++) {
            $area = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaFlexInterface');
            $columnArea[] = $area;
        }
        Phake::when($this->rowArea)->getAreas()->thenReturn($columnArea);
        $data = array(
            "columnLayout" => array("layout" => $layout),
            "areaId" => "fakeAreaID"
        );
        Phake::when($this->event)->getData()->thenReturn($data);

        $this->subscriber->preSubmit($this->event);

        $this->assertCount($layoutCount, $this->rowArea->getAreas());
    }

    /**
     * @return array
     */
    public function provideAreaCountAndLayout()
    {
        return array(
            array(0, '1', 1),
            array(5, '1,1,1', 3),
            array(3, '1,10px,5', 3),
            array(1, '1,10px', 2),
            array(10, '1,auto', 2),
            array(2, ' 1, auto', 2),
        );
    }

    /**
     * Test subscribed events
     */
    public function testEventSubscribed()
    {
        $this->assertArrayHasKey(FormEvents::PRE_SUBMIT, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(FormEvents::PRE_SET_DATA, $this->subscriber->getSubscribedEvents());
    }
}
