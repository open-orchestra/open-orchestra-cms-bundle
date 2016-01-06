<?php

namespace OpenOrchestra\Backoffice\Tests\ValueTransformer\Strategies;

use OpenOrchestra\Backoffice\ValueTransformer\Strategies\IntegerToHtmlStringTransformer;

/**
 * Class IntegerToHtmlStringTransformerTest
 */
class IntegerToHtmlStringTransformerTest extends AbstractTransformerTest
{
    /**
     * Set up Tests
     */
    public function setUp()
    {
        $this->transformer = new IntegerToHtmlStringTransformer();
    }

    /**
     * @return array
     */
    public function provideTransform()
    {
        return array(
            array('0', '0'),
            array(0, '0'),
        );
    }

    /**
     * @return array
     */
    public function provideSupport()
    {
        return array(
            array('integer', 0, true),
            array('integer', '0', false),
            array('string', 0, false),
            array('string', array(), false),
        );
    }

    /**
     * @return string
     */
    protected function getTransformerName()
    {
        return 'integer';
    }
}
