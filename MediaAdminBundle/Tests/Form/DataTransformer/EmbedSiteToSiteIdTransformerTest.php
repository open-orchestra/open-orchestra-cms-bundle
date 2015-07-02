<?php

namespace OpenOrchestra\MediaAdminBundle\Tests\Form\DataTransformer;

use OpenOrchestra\MediaAdminBundle\Form\DataTransformer\EmbedSiteToSiteIdTransformer;
use Phake;

/**
 * Test EmbedSiteToSiteIdTransformerTest
 */
class EmbedSiteToSiteIdTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EmbedSiteToSiteIdTransformer
     */
    protected $transformer;

    protected $site;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        $this->transformer = new EmbedSiteToSiteIdTransformer();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $this->transformer);
    }

    /**
     * @param array $input
     * @param array $output
     *
     * @dataProvider provideTransformData
     */
    public function testTransform($input, $output)
    {
        $this->assertSame($output, $this->transformer->transform($input));
    }

    /**
     * @return array
     */
    public function provideTransformData()
    {
        return array(
            array(array(), array()),
            array(array(array('siteId' => 'foo')), array('foo')),
            array(array(array('siteId' => 'foo'), array('siteId' => 'bar')), array('foo', 'bar')),
        );
    }

    /**
     * @param array $output
     * @param array $input
     *
     * @dataProvider provideTransformData
     */
    public function testReverseTransform($output, $input)
    {
        $this->assertSame($output, $this->transformer->reverseTransform($input));
    }
}
