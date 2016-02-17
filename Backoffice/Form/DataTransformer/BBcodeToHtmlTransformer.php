<?php

namespace OpenOrchestra\Backoffice\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use OpenOrchestra\BBcodeBundle\Parser\BBcodeParserInterface;

/**
 * Class BBcodeToHtmlTransformer
 */
class BBcodeToHtmlTransformer implements DataTransformerInterface
{
    protected $parser;

    /**
     * @param BBcodeParserInterface $parser
     */
    public function __construct(BBcodeParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Take a BBcode string to turn it into html
     * 
     * @param string $bbCode
     * 
     * @return string
     */
    public function transform($bbCode)
    {
        $this->parser->parse($bbCode);

        return $this->parser->getAsPreviewHTML();
    }

    /**
     * @param string $html
     * 
     * @return string
     */
    public function reverseTransform($html)
    {
        return $html;
    }
}
