<?php

namespace OpenOrchestra\BackofficeBundle\Tests\ValueTransformer\Strategies;

use OpenOrchestra\Backoffice\ValueTransformer\Strategies\DocumentToHtmlStringTransformer;

/**
 * Class DocumentToHtmlStringTransformerTest
 */
class DocumentToHtmlStringTransformerTest extends AbstractTransformerTest
{
    protected $property = 'fakeProperty';

    /**
     * Set up Tests
     */
    public function setUp()
    {
        $this->transformer = new DocumentToHtmlStringTransformer($this->property);
    }

    /**
     * @return array
     */
    public function provideTransform()
    {
        return array(
            array(
                array(
                    $this->property => 'foo'
                ),
                'foo'),
        );
    }

    /**
     * @return array
     */
    public function provideSupport()
    {
        return array(
            array('document', '', true),
            array('', '', false),
        );
    }

    /**
     * @return string
     */
    protected function getTransformerName()
    {
        return 'document';
    }

}
