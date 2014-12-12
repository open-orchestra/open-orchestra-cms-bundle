<?php

namespace PHPOrchestra\BackofficeBundle\Test\Generator;

use PHPOrchestra\Backoffice\Generator\BlockGenerator;

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

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->skeletonDir[] = __DIR__ . '/files/skeleton/';

        $this->generator = new BlockGenerator();
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
        $dir = __DIR__ . '/files/generated';
        $referenceDir = __DIR__ . '/files/reference';
        $this->assertDirectoryEmpty($dir);

        $backofficeDisplayDir = $dir . '/backofficeDisplayBlock';
        $frontDisplayDir = $dir . '/frontDisplayBlock';
        $displayIconDir = $dir . '/displayIcon';
        $generateFormDir = $dir . '/generateForm';

        $this->generator->generate(
            'test',
            $generateFormDir,
            'PHPOrchestra\Backoffice',
            $frontDisplayDir,
            'PHPOrchestra\DisplayBundle',
            $displayIconDir,
            'PHPOrchestra\BackofficeBundle',
            $backofficeDisplayDir,
            'PHPOrchestra\BackofficeBundle');

        $this->assertFileEquals($referenceDir . '/backofficeDisplayBlock/TestStrategy.php', $backofficeDisplayDir . '/TestStrategy.php');
        $this->assertFileEquals($referenceDir . '/frontDisplayBlock/TestStrategy.php', $frontDisplayDir . '/TestStrategy.php');
        $this->assertFileEquals($referenceDir . '/displayIcon/TestStrategy.php', $displayIconDir . '/TestStrategy.php');
        $this->assertFileEquals($referenceDir . '/generateForm/TestStrategy.php', $generateFormDir . '/TestStrategy.php');
        $this->assertFileEquals($referenceDir . '/Resources/views/Block/Test/show.html.twig', $dir . '/../Resources/views/Block/Test/show.html.twig');
        $this->assertFileEquals($referenceDir . '/Resources/views/Block/Test/showIcon.html.twig', $dir . '/../Resources/views/Block/Test/showIcon.html.twig');
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
