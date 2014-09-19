<?php

namespace PHPOrchestra\BackofficeBundle\Test\Manager;

use PHPOrchestra\BackofficeBundle\Manager\AreaManager;
use PHPOrchestra\ModelBundle\Document\Area;
use Phake;

/**
 * Class AreaManagerTest
 */
class AreaManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AreaManager
     */
    protected $manager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->manager = new AreaManager(Phake::mock('Doctrine\ODM\MongoDB\DocumentManager'));
    }

    /**
     * @param array  $areas
     * @param string $areaId
     * @param array  $expectedAreas
     *
     * @dataProvider provideAreasAndAreaId
     */
    public function testDeleteAreaFromAreas($areas, $areaId, $expectedAreas)
    {
        $alteredAreas = $this->manager->deleteAreaFromAreas($areas, $areaId);

        $this->assertSame($expectedAreas, $alteredAreas);
    }

    /**
     * @return array
     */
    public function provideAreasAndAreaId()
    {
        $area1 = new Area(); $area1->setAreaId('area1');
        $area2 = new Area(); $area2->setAreaId('area2');
        $area3 = new Area(); $area3->setAreaId('area3');
        
        $areas = array(
            'area1' => $area1,
            'area2' => $area2,
            'area3' => $area3
        );
        $filteredAreas = array(
            'area1' => $area1,
            'area3' => $area3
        );

        return array(
            array(array(), 'miscId', array()),
            array($areas, 'miscId', $areas),
            array($areas, 'area2', $filteredAreas)
        );
    }
}
