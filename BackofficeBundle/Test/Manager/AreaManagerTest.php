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
     * dummy test
     */
    public function test()
    {
        $this->assertTrue(true);
    }
}
