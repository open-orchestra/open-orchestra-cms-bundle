<?php

namespace PHPOrchestra\BackofficeBundle\Test\ExtractReference\Strategies;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use PHPOrchestra\Backoffice\ExtractReference\Strategies\ExtractReferenceFromContentStrategy;

/**
 * Test ExtractReferenceFromContentStrategyTest
 */
class ExtractReferenceFromContentStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExtractReferenceFromContentStrategy
     */
    protected $strategy;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->strategy = new ExtractReferenceFromContentStrategy();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('PHPOrchestra\Backoffice\ExtractReference\ExtractReferenceInterface', $this->strategy);
    }

    /**
     * test Name
     */
    public function testName()
    {
        $this->assertSame('content', $this->strategy->getName());
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
            array('PHPOrchestra\ModelInterface\Model\NodeInterface', false),
            array('PHPOrchestra\ModelInterface\Model\ContentInterface', true),
            array('PHPOrchestra\ModelInterface\Model\ContentTypeInterface', false),
            array('PHPOrchestra\ModelInterface\Model\StatusableInterface', false),
        );
    }

    /**
     * Test extract
     */
    public function testExtractReference()
    {
        $contentAttribute1 = Phake::mock('PHPOrchestra\ModelInterface\Model\ContentAttributeInterface');
        $contentAttribute2 = Phake::mock('PHPOrchestra\ModelInterface\Model\ContentAttributeInterface');
        $contentAttributes = new ArrayCollection();
        $contentAttributes->add($contentAttribute1);
        $contentAttributes->add($contentAttribute2);

        $contentId = 'contentId';
        $content = Phake::mock('PHPOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content)->getId()->thenReturn($contentId);
        Phake::when($content)->getAttributes()->thenReturn($contentAttributes);

        Phake::when($contentAttribute1)->getValue()->thenReturn('media-foo');
        Phake::when($contentAttribute2)->getValue()->thenReturn('class2');

        $expected = array(
            'foo' => array('content-' . $contentId),
        );

        $this->assertSame($expected, $this->strategy->extractReference($content));
    }
}
