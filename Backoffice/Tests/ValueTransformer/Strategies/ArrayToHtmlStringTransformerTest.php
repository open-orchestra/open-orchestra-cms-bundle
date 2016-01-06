<?php

namespace OpenOrchestra\Backoffice\Tests\ValueTransformer\Strategies;

use OpenOrchestra\Backoffice\ValueTransformer\Strategies\ArrayToHtmlStringTransformer;

/**
 * Class ArrayToHtmlStringTransformerTest
 */
class ArrayToHtmlStringTransformerTest extends AbstractTransformerTest
{
    /**
     * Set up Tests
     */
    public function setUp()
    {
        $this->transformer = new ArrayToHtmlStringTransformer();
    }

    /**
     * @return array
     */
    public function provideTransform()
    {
        return array(
            array('foo', 'foo'),
            array(array('foo', 'bar'), '<ul><li>foo</li><li>bar</li></ul>'),
            array(array(), '<ul></ul>'),
        );
    }

    /**
     * @return array
     */
    public function provideSupport()
    {
        return array(
            array('choice', array(), true),
            array('choice', 'array', false),
            array('array', array(), false),
        );
    }

    /**
     * @return string
     */
    protected function getTransformerName()
    {
        return 'array';
    }
}
