<?php

namespace OpenOrchestra\BackofficeBundle\Test\Twig;

use Phake;
use OpenOrchestra\BackofficeBundle\Twig\IsFolderDeletableExtension;

/**
 * Class IsFolderDeletableExtension
 */
class IsFolderDeletableExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $helper;
    protected $folderManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->folderManager = Phake::mock('OpenOrchestra\BackofficeBundle\Manager\FolderManager');
        $this->helper = new IsFolderDeletableExtension($this->folderManager);
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
        $folder = Phake::mock('OpenOrchestra\Media\Model\MediaFolderInterface');

        Phake::when($this->folderManager)->isDeletable(Phake::anyParameters())->thenReturn(true);
        $this->assertEquals($this->helper->isFolderDeletable($folder), true);

        Phake::when($this->folderManager)->isDeletable(Phake::anyParameters())->thenReturn(false);
        $this->assertEquals($this->helper->isFolderDeletable($folder), false);
    }
}
