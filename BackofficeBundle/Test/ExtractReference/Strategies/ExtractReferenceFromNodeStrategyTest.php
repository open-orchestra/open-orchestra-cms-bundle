<?php

namespace PHPOrchestra\BackofficeBundle\Test\ExtractReference\Strategies;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use PHPOrchestra\Backoffice\ExtractReference\Strategies\ExtractReferenceFromNodeStrategy;
use PHPOrchestra\Media\Model\MediaInterface;

/**
 * Class ExtractReferenceFromNodeStrategyTest
 */
class ExtractReferenceFromNodeStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExtractReferenceFromNodeStrategy
     */
    protected $strategy;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->strategy = new ExtractReferenceFromNodeStrategy();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('PHPOrchestra\Backoffice\ExtractReference\ExtractReferenceInterface', $this->strategy);
    }

    /**
     * @param string $class
     * @param bool   $support
     *
     * @dataProvider provideClassAndSupport
     */
    public function testSupport($class, $support)
    {
        $this->assertSame($support, $this->strategy->support(Phake::mock($class)));
    }

    /**
     * @return array
     */
    public function provideClassAndSupport()
    {
        return array(
            array('PHPOrchestra\ModelInterface\Model\NodeInterface', true),
            array('PHPOrchestra\ModelInterface\Model\ContentInterface', false),
            array('PHPOrchestra\ModelInterface\Model\ContentTypeInterface', false),
            array('PHPOrchestra\ModelInterface\Model\StatusableInterface', false),
        );
    }

    /**
     * Test extract
     */
    public function testExtractReference()
    {
        $block1 = Phake::mock('PHPOrchestra\ModelInterface\Model\BlockInterface');
        $block2 = Phake::mock('PHPOrchestra\ModelInterface\Model\BlockInterface');
        $block3 = Phake::mock('PHPOrchestra\ModelInterface\Model\BlockInterface');
        $blocks = new ArrayCollection();
        $blocks->add($block1);
        $blocks->add($block2);
        $blocks->add($block3);

        $nodeId = 'nodeId';
        $node = Phake::mock('PHPOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getId()->thenReturn($nodeId);
        Phake::when($node)->getBlocks()->thenReturn($blocks);

        Phake::when($block1)->getAttributes()->thenReturn(array(
            'id' => 'id',
            'class' => 'class',
            'media' => MediaInterface::MEDIA_PREFIX . 'foo',
        ));
        Phake::when($block2)->getAttributes()->thenReturn(array(
            'id' => 'id2',
            'class' => 'class2',
            'media1' => MediaInterface::MEDIA_PREFIX . 'foo',
            'media2' => MediaInterface::MEDIA_PREFIX . 'bar',
        ));
        Phake::when($block3)->getAttributes()->thenReturn(array(
            'id' => 'id3',
            'class' => 'class3',
            'mediaSingle' => MediaInterface::MEDIA_PREFIX . 'bar',
            'mediaCollection' => array(
                MediaInterface::MEDIA_PREFIX . 'foo_col',
                MediaInterface::MEDIA_PREFIX . 'bar_col'
            )
        ));
        
        $expected = array(
            'foo' => array('node-' . $nodeId . '-0', 'node-' . $nodeId . '-1'),
            'bar' => array('node-' . $nodeId . '-1', 'node-' . $nodeId . '-2'),
            'foo_col' => array('node-' . $nodeId . '-2'),
            'bar_col' => array('node-' . $nodeId . '-2'),
        );

        $this->assertSame($expected, $this->strategy->extractReference($node));
    }

    /**
     * test name
     */
    public function testName()
    {
        $this->assertSame('node', $this->strategy->getName());
    }
}
