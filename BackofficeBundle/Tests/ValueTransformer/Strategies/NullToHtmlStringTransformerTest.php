<?php

namespace OpenOrchestra\BackofficeBundle\Tests\ValueTransformer\Strategies;

use OpenOrchestra\Backoffice\ValueTransformer\Strategies\NullToHtmlStringTransformer;

/**
 * Class NullToHtmlStringTransformerTest
 */
class NullToHtmlStringTransformerTest extends AbstractTransformerTest
{
    /**
     * Set up Tests
     */
    public function setUp()
    {
        $this->transformer = new NullToHtmlStringTransformer();
    }

    /**
     * @return array
     */
    public function provideTransform()
    {
        return array(
            array(null, 'none'),
        );
    }

    /**
     * @return array
     */
    public function provideSupport()
    {
        return array(
            array('', null, true),
            array('', 'null', false),
            array('', 0, false),
            array('', array(), false),
        );
    }

    /**
     * @return string
     */
    protected function getTransformerName()
    {
        return 'null';
    }
}
