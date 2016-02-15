<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Manager;

use OpenOrchestra\BackofficeBundle\Manager\AreaFlexManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;

/**
 * Class AreaFlexManagerTest
 */
class AreaFlexManagerTest extends AbstractBaseTestCase
{
    /**
     * @var AreaFlexManager
     */
    protected $manager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $areaFlexClass = 'OpenOrchestra\ModelBundle\Document\AreaFlex';
        $this->manager = new AreaFlexManager(
            $areaFlexClass
        );
    }

    /**
     * Test initialize new area row
     */
    public function testInitializeNewAreaRow()
    {
        $area = $this->manager->initializeNewAreaRow();

        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\AreaFlexInterface', $area);
        $this->assertEquals($area->getAreaType(), AreaFlexInterface::TYPE_ROW);
    }

    /**
     * Test initialize new area column
     */
    public function testInitializeNewAreaColumn()
    {
        $area = $this->manager->initializeNewAreaColumn();

        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\AreaFlexInterface', $area);
        $this->assertEquals($area->getAreaType(), AreaFlexInterface::TYPE_COLUMN);
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
