<?php

namespace OpenOrchestra\Backoffice\Tests\Manager;

use OpenOrchestra\Backoffice\Manager\AreaFlexManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;
use Phake;

/**
 * Class AreaFlexManagerTest
 */
class AreaFlexManagerTest extends AbstractBaseTestCase
{
    /**
     * @var AreaFlexManager
     */
    protected $manager;
    protected $fakeParentId = 'fake_parent_id';
    protected $parentArea;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $areaFlexClass = 'OpenOrchestra\ModelBundle\Document\AreaFlex';
        $this->parentArea = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaFlexInterface');
        Phake::when($this->parentArea)->getAreas()->thenReturn(array());
        Phake::when($this->parentArea)->getAreaId()->thenReturn($this->fakeParentId);

        $this->manager = new AreaFlexManager(
            $areaFlexClass
        );
    }

    /**
     * Test initialize new area row
     */
    public function testInitializeNewAreaRow()
    {
        $area = $this->manager->initializeNewAreaRow($this->parentArea);

        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\AreaFlexInterface', $area);
        $this->assertEquals($area->getAreaType(), AreaFlexInterface::TYPE_ROW);
        $this->assertEquals($area->getAreaId(), $this->fakeParentId.'_'.AreaFlexInterface::TYPE_ROW.'_1');
    }

    /**
     * Test initialize new area column
     */
    public function testInitializeNewAreaColumn()
    {
        $area = $this->manager->initializeNewAreaColumn($this->parentArea);

        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\AreaFlexInterface', $area);
        $this->assertEquals($area->getAreaType(), AreaFlexInterface::TYPE_COLUMN);
        $this->assertEquals($area->getAreaId(), $this->fakeParentId.'_'.AreaFlexInterface::TYPE_COLUMN.'_1');
    }

    /**
     * Test initialize new area root
     */
    public function testInitializeNewAreaRoot()
    {
        $area = $this->manager->initializeNewAreaRoot();

        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\AreaFlexInterface', $area);
        $this->assertEquals($area->getAreaType(), AreaFlexInterface::TYPE_ROOT);
        $this->assertEquals($area->getAreaId(), AreaFlexInterface::ROOT_AREA_ID);
        $this->assertEquals($area->getLabel(), AreaFlexInterface::ROOT_AREA_LABEL);
    }
}
