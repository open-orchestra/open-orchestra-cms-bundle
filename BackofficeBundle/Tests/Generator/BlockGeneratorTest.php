<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Generator;

use OpenOrchestra\Backoffice\Generator\BlockGenerator;

/**
 * Class BlockGeneratorTest
 */
class BlockGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BlockGenerator
     */
    protected $generator;

    protected $skeletonDir = array();
    protected $rootDir;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->rootDir = __DIR__ . '/files/generated';
        $this->skeletonDir[] = __DIR__ . '/files/skeleton/';

        $this->generator = new BlockGenerator($this->rootDir);
        $this->generator->setSkeletonDirs($this->skeletonDir);
    }

    /**
     * Test intance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Sensio\Bundle\GeneratorBundle\Generator\Generator', $this->generator);
    }

    /**
     * Test generate
     */
    public function testGenerate()
    {
        $referenceDir = __DIR__ . '/files/reference';
        $this->assertDirectoryEmpty($this->rootDir);

        $backofficeDisplayDir = 'backofficeDisplayBlock';
        $frontDisplayDir = 'frontDisplayBlock';
        $displayIconDir = 'displayIcon';
        $generateFormDir = 'generateForm';

        $this->generator->generate(
            'test',
            $generateFormDir,
            'OpenOrchestra\Backoffice',
            $frontDisplayDir,
            'OpenOrchestra\DisplayBundle',
            $displayIconDir,
            'OpenOrchestra\BackofficeBundle',
            $backofficeDisplayDir,
            'OpenOrchestra\BackofficeBundle');

        $this->assertFileEquals($referenceDir . '/backofficeDisplayBlock/TestStrategy.php', $this->rootDir . '/' . $backofficeDisplayDir . '/TestStrategy.php');
        $this->assertFileEquals($referenceDir . '/frontDisplayBlock/TestStrategy.php', $this->rootDir . '/' . $frontDisplayDir . '/TestStrategy.php');
        $this->assertFileEquals($referenceDir . '/displayIcon/TestStrategy.php', $this->rootDir . '/' . $displayIconDir . '/TestStrategy.php');
        $this->assertFileEquals($referenceDir . '/generateForm/TestStrategy.php', $this->rootDir . '/' . $generateFormDir . '/TestStrategy.php');
        $this->assertFileEquals($referenceDir . '/Resources/views/Block/Test/show.html.twig', $this->rootDir . '/../Resources/views/Block/Test/show.html.twig');
        $this->assertFileEquals($referenceDir . '/Resources/views/Block/Test/showIcon.html.twig', $this->rootDir . '/../Resources/views/Block/Test/showIcon.html.twig');
    }

    /**
     * @param string $dir
     */
    protected function assertDirectoryEmpty($dir)
    {
        system("rm -rf " . escapeshellarg($dir));
        system("rm -rf " . escapeshellarg($dir . '/../Resources'));

        $this->assertFileNotExists($dir . '/backofficeDisplayBlock/TestStrategy.php');
        $this->assertFileNotExists($dir . '/displayIcon/TestStrategy.php');
        $this->assertFileNotExists($dir . '/frontDisplayBlock/TestStrategy.php');
        $this->assertFileNotExists($dir . '/generateform/TestStrategy.php');
        $this->assertFileNotExists($dir . '/../Resources/views/Block/Test/show.html.twig');
        $this->assertFileNotExists($dir . '/../Resources/views/Block/Test/showIcon.html.twig');
    }
}
