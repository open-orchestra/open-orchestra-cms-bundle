<?php

namespace PHPOrchestra\BackofficeBundle\Test\Manager;

use PHPOrchestra\ModelBundle\Model\AreaContainerInterface;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @param AreaContainerInterface $areaContainer
     * @param string                 $areaId
     * @param AreaContainerInterface $expectedArea
     *
     * @dataProvider provideAreaAndAreaId
     */
    public function testDeleteAreaFromAreas(AreaContainerInterface $areaContainer, $areaId, AreaContainerInterface $expectedArea)
    {
        $this->manager->deleteAreaFromAreas($areaContainer, $areaId);

        $this->assertTrue(
            $this->array_contains($expectedArea->getAreas(), $areaContainer->getAreas())
            && $this->array_contains($areaContainer->getAreas(), $expectedArea->getAreas())
        );
    }

    /**
     * Check if values of $includedArray are present in $refArray
     * 
     * @param array $refArray
     * @param array $includedArray
     */
    protected function array_contains(ArrayCollection $refArray, ArrayCollection $includedArray) {
        $res = true;

        if (count($includedArray) > 0) {
            foreach($includedArray as $element) {
                if (!$refArray->contains($element)) {
                    $res = false;
                    break;
                }
            }
        }

        return $res;
    }

    /**
     * @return array
     */
    public function provideAreaAndAreaId()
    {
        $area1 = Phake::mock('PHPOrchestra\ModelBundle\Model\AreaInterface');
        Phake::when($area1)->getAreaId()->thenReturn('area1');

        $area2 = Phake::mock('PHPOrchestra\ModelBundle\Model\AreaInterface');
        Phake::when($area2)->getAreaId()->thenReturn('area2');

        $area3 = Phake::mock('PHPOrchestra\ModelBundle\Model\AreaInterface');
        Phake::when($area3)->getAreaId()->thenReturn('area3');

        $emptyArea = new Area();

        $area = new Area();
        $area->addArea($area1); $area->addArea($area2); $area->addArea($area3);

        $filteredArea = new Area();
        $filteredArea->addArea($area1); $filteredArea->addArea($area3);

        return array(
            array($emptyArea, 'miscId', $emptyArea),
            array($area, 'miscId', $area),
            array($area, 'area2', $filteredArea)
        );
    }
}
