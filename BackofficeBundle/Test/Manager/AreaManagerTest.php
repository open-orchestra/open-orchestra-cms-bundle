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
        $this->manager = new AreaManager();
    }

    /**
     * @param Area   $area
     * @param int    $blockPosition
     * @param array   $expectedBlocks
     *
     * @dataProvider provideAreasAndBlockPosition
     */
    public function testRemoveBlockFromArea(Area $area, $blockPosition, array $expectedBlocks)
    {
        $alteredArea = $this->manager->removeBlockFromArea($area, $blockPosition);

        $this->assertSame($alteredArea->getBlocks(), $expectedBlocks);
    }

    /**
     * @return array
     */
    public function provideAreasAndBlockPosition()
    {
        $area = new Area();
        $blocks = array(0 => 'Block1', 1 => 'Block2', 2 => 'Block3');
        $area->setBlocks($blocks);

        return array(
            array(new Area(), 5, array()),
            array($area, 5, $blocks),
            array($area, 1, array(0 => 'Block1', 2 => 'Block3'))
        );
    }
}
