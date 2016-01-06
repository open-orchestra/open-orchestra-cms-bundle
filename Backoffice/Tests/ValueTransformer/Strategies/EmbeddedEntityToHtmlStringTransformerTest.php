<?php

namespace OpenOrchestra\Backoffice\Tests\ValueTransformer\Strategies;

use OpenOrchestra\Backoffice\ValueTransformer\Strategies\EmbeddedEntityToHtmlStringTransformer;

/**
 * Class DocumentToHtmlStringTransformerTest
 */
class EmbeddedEntityToHtmlStringTransformerTest extends AbstractTransformerTest
{
    protected $property = 'fakeProperty';

    /**
     * Set up Tests
     */
    public function setUp()
    {
        $this->transformer = new EmbeddedEntityToHtmlStringTransformer($this->property);
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
            array('embedded_content', '', true),
            array('', '', false),
        );
    }

    /**
     * @return string
     */
    protected function getTransformerName()
    {
        return 'embedded_entity';
    }

}
