<?php

namespace PHPOrchestra\BackofficeBundle\Test\DataTransformer;

use Phake;
use PHPOrchestra\BackofficeBundle\Form\DataTransformer\VideoUrlToIdTransformer;

/**
 * Class VideoUrlToIdTransformerTest
 */
class VideoUrlToIdTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var VideoUrlToIdTransformer
     */
    protected $transformer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->transformer = new VideoUrlToIdTransformer();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $this->transformer);
    }

    /**
     * @param string $url
     * @param string $id
     *
     * @dataProvider provideVideoUrl
     */
    public function testTransform($url, $id)
    {
        $this->assertSame($id, $this->transformer->transform($url));
    }

    /**
     * @return array
     */
    public function provideVideoUrl()
    {
        return array(
            array('https://www.youtube.com/watch?v=qJbG7ROGj-I', 'qJbG7ROGj-I'),
            array('http://youtu.be/qJbG7ROGj-I', 'qJbG7ROGj-I'),
            array('http://www.dailymotion.com/video/x2ec4jf_francois-fillon-deplore-une-polemique-detestable-autour-du-fn_news', 'x2ec4jf_francois-fillon-deplore-une-polemique-detestable-autour-du-fn_news'),
            array('http://dai.ly/x2ec4jf', 'x2ec4jf'),
            array('http://vimeo.com/channels/staffpicks/116044343', '116044343'),
            array('116044343', '116044343'),
            array('', ''),
        );
    }

    /**
     * Test reverseTransform
     */
    public function testResverseTransform()
    {
        $videoId = '116044343';
        $this->assertSame($videoId, $this->transformer->reverseTransform($videoId));
    }
}
