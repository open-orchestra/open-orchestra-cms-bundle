<?php

namespace PHPOrchestra\BackofficeBundle\Test\Twig;

use Phake;
use PHPOrchestra\BackofficeBundle\Twig\IsFolderDeletableExtension;

/**
 * Class IsFolderDeletableExtension
 */
class IsFolderDeletableExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $helper;
    protected $folderManager;
    protected $folderRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->folderManager = Phake::mock('PHPOrchestra\BackofficeBundle\Manager\FolderManager');
        $this->helper = new IsFolderDeletableExtension($this->folderManager, $this->folderRepository);
    }

    /**
     * test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Twig_Extension', $this->helper);
    }

    /**
     */
    public function testIsFolderDeletable()
    {
        Phake::when($this->folderManager)->isDeletable(Phake::anyParameters())->thenReturn(true);
        $this->assertEquals($this->helper->isFolderDeletable(Phake::anyParameters()), true);

        Phake::when($this->folderManager)->isDeletable(Phake::anyParameters())->thenReturn(false);
        $this->assertEquals($this->helper->isFolderDeletable(Phake::anyParameters()), false);
    }
}
