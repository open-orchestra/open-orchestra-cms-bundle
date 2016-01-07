<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Twig;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\BackofficeBundle\Twig\BlockIconExtension;

/**
 * Class BlockIconExtensionTest
 */
class BlockIconExtensionTest extends AbstractBaseTestCase
{
    protected $twig;
    protected $iconManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->iconManager = Phake::mock('OpenOrchestra\BackofficeBundle\DisplayIcon\DisplayManager');

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
