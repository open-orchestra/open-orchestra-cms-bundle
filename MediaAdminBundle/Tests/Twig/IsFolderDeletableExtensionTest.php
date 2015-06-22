<?php

namespace OpenOrchestra\MediaAdminBundle\Tests\Twig;

use Phake;
use OpenOrchestra\MediaAdminBundle\Twig\IsFolderDeletableExtension;

/**
 * Class IsFolderDeletableExtension
 */
class IsFolderDeletableExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IsFolderDeletableExtension
     */
    protected $helper;

    protected $folderManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->folderManager = Phake::mock('OpenOrchestra\MediaAdminBundle\Manager\FolderManager');
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
     * Test is folder deletable
     */
    public function testIsFolderDeletable()
    {
        $folder = Phake::mock('OpenOrchestra\Media\Model\MediaFolderInterface');

        Phake::when($this->folderManager)->isDeletable(Phake::anyParameters())->thenReturn(true);
        $this->assertEquals($this->helper->isFolderDeletable($folder), true);

        Phake::when($this->folderManager)->isDeletable(Phake::anyParameters())->thenReturn(false);
        $this->assertEquals($this->helper->isFolderDeletable($folder), false);
    }

    /**
     * Test get functions
     */
    public function testGetFunctions()
    {
        $functions = $this->helper->getFunctions();

        $this->assertCount(1, $functions);
        $this->assertInstanceOf('Twig_SimpleFunction', $functions[0]);
    }

    /**
     * Test name
     */
    public function testGetName()
    {
        $this->assertSame('is_folder_deletable', $this->helper->getName());
    }
}
