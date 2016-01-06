<?php

namespace OpenOrchestra\Backoffice\Tests\ValueTransformer\Strategies;

use OpenOrchestra\Backoffice\ValueTransformer\Strategies\ObjectToHtmlStringTransformer;

/**
 * Class ObjectToHtmlStringTransformerTest
 */
class ObjectToHtmlStringTransformerTest extends AbstractTransformerTest
{
    /**
     * Set up Tests
     */
    public function setUp()
    {
        $this->transformer = new ObjectToHtmlStringTransformer();
    }

    /**
     * @return array
     */
    public function provideTransform()
    {
        return array(
            array(new ObjectWithToString(), 'foo'),
        );
    }

    /**
     * @return array
     */
    public function provideSupport()
    {
        return array(
            array('', new ObjectWithToString(), true),
            array('', 'foo', false),
            array('', 0, false),
            array('', array(), false),
        );
    }

    /**
     * @return string
     */
    protected function getTransformerName()
    {
        return 'object';
    }

}

/**
 * Class ObjectWithToString
 */
class ObjectWithToString
{
    /**
     * @return string
     */
    public function __toString()
    {
        return 'foo';
    }
}
