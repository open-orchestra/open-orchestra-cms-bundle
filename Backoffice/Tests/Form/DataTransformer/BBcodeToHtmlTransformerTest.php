<?php

namespace OpenOrchestra\Backoffice\Tests\Form\BBcodeToHtmlTrandformerTest;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\DataTransformer\BBcodeToHtmlTransformer;

/**
 * Class BBcodeToHtmlTransformerTest
 */
class BBcodeToHtmlTransformerTest extends AbstractBaseTestCase
{
    /**
     * @var BBcodeToHtmlTransformer
     */
    protected $transformer;

    protected $parser;
    protected $html;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->parser = Phake::mock('OpenOrchestra\BBcodeBundle\Parser\BBcodeParser');
        $this->transformer = new BBcodeToHtmlTransformer($this->parser);

        $this->html = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer tempor non sapien vitae feugiat.
            Duis rhoncus, purus non consectetur efficitur, orci nisi posuere neque, eu ultricies erat risus ac ante.';
    }

    /**
     * Test Transform
     */
    public function testTransform()
    {
        $html = $this->transformer->transform($this->html);

        Phake::verify($this->parser)->parse($this->html);
        Phake::verify($this->parser)->getAsPreviewHTML();
    }

    /**
     * Test reverseTransform
     */
    public function testReverseTransform()
    {
        $bbcode = $this->transformer->reverseTransform($this->html);

        $this->assertSame($this->html, $bbcode);
    }
}
