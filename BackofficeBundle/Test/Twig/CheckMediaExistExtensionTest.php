<?php

namespace PHPOrchestra\BackofficeBundle\Twig;

use Phake;
use PHPOrchestra\BackofficeBundle\Twig\CheckMediaExistExtension;

/**
 * Class CheckMediaExistExtension
 */
class CheckMediaExistExtensionTest extends \PHPUnit_Framework_TestCase
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
        $this->folderRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\FolderRepository');
        $this->helper = new CheckMediaExistExtension($this->folderManager, $this->folderRepository);
    }

    /**
     * test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Twig_Extension', $this->helper);
    }

    /**
     * @param int     $countMedia
     * @param boolean $expectedResult
     *
     * @dataProvider provideCountMedia
     */
    public function testCheckMediaExist($countMedia, $expectedResult)
    {

        $folder = Phake::mock('PHPOrchestra\ModelBundle\Model\MediaFolderInterface');
        Phake::when($this->folderRepository)->find(Phake::anyParameters())->thenReturn($folder);
        Phake::when($this->folderManager)->countMediaTree($folder)->thenReturn($countMedia);

        $this->assertEquals($this->helper->checkMediaExist(''), $expectedResult);

    }

    /**
     * @return array
     */
    public function provideCountMedia()
    {

        return array(
            array(0, false),
            array(1, true),
        );
    }


}
