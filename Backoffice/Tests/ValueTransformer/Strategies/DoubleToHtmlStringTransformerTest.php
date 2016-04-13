<?php

namespace OpenOrchestra\Backoffice\Tests\ValueTransformer\Strategies;

use OpenOrchestra\Backoffice\ValueTransformer\Strategies\DoubleToHtmlStringTransformer;

/**
 * Class DoubleToHtmlStringTransformerTest
 */
class DoubleToHtmlStringTransformerTest extends AbstractTransformerTest
{
    /**
     * Set up Tests
     */
    public function setUp()
    {
        $this->transformer = new DoubleToHtmlStringTransformer();
    }

    /**
     * @return array
     */
    public function provideTransform()
    {
        return array(
            array('5.5', '5.5'),
            array(1.5555, '1.5555'),
            array(5.5, '5.5'),
        );
    }

    /**
     * @return array
     */
    public function provideSupport()
    {
        return array(
            array('money', 1.5, true),
            array('money', 5, false),
            array('integer', 1, false),
            array('string', 0, false),
            array('string', array(), false),
        );
    }

    /**
     * @return string
     */
    protected function getTransformerName()
    {
        return 'double';
    }
}
