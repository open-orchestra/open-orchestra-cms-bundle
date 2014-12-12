<?php

namespace PHPOrchestra\BackofficeBundle\Test\Twig;

use Phake;
use PHPOrchestra\BackofficeBundle\Twig\BlockIconExtension;

/**
 * Class BlockIconExtensionTest
 */
class BlockIconExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $twig;
    protected $iconManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->iconManager = Phake::mock('PHPOrchestra\BackofficeBundle\DisplayIcon\DisplayManager');

        $this->twig = new BlockIconExtension($this->iconManager);
    }

    /**
     * test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Twig_Extension', $this->twig);
    }

    /**
     * Test displayIcon
     */
    public function testDisplayIcon()
    {
        Phake::when($this->iconManager)->show(Phake::anyParameters())->thenReturn('icon');
        $this->assertEquals($this->twig->displayIcon('sample'), 'icon');
    }
}
