<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type\Component;

use OpenOrchestra\Backoffice\GeneratePerimeter\GeneratePerimeterManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class GeneratePerimeterManagerTest
 */
class GeneratePerimeterManagerTest extends AbstractBaseTestCase
{
    protected $manager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->manager = new GeneratePerimeterManager();
    }

    /**
     * Test generatePerimeters
     */
    public function testGeneratePerimeters()
    {
        $key0 = 'fakeKey0';
        $perimeter0 = array('fakePerimeter0');
        $strategy0 = Phake::mock('OpenOrchestra\Backoffice\GeneratePerimeter\Strategy\GeneratePerimeterStrategyInterface');
        Phake::when($strategy0)->getType()->thenReturn($key0);
        Phake::when($strategy0)->generatePerimeter(Phake::anyParameters())->thenReturn($perimeter0);
        Phake::when($strategy0)->getPerimeterConfiguration(Phake::anyParameters())->thenReturn($perimeter0);
        $this->manager->addStrategy($strategy0);

        $key1 = 'fakeKey1';
        $perimeter1 = array('fakePerimeter1');
        $strategy1 = Phake::mock('OpenOrchestra\Backoffice\GeneratePerimeter\Strategy\GeneratePerimeterStrategyInterface');
        Phake::when($strategy1)->getType()->thenReturn($key1);
        Phake::when($strategy1)->generatePerimeter(Phake::anyParameters())->thenReturn($perimeter1);
        Phake::when($strategy1)->getPerimeterConfiguration(Phake::anyParameters())->thenReturn($perimeter1);
        $this->manager->addStrategy($strategy1);

        $this->assertEquals(array($key0 => $perimeter0, $key1 => $perimeter1), $this->manager->generatePerimeters('siteId'));
        $this->assertEquals(array($key0 => $perimeter0, $key1 => $perimeter1), $this->manager->getPerimetersConfiguration('siteId'));
    }
}
