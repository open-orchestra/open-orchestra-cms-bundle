<?php

namespace OpenOrchestra\Backoffice\Tests\Perimeter\Strategy;

use OpenOrchestra\Backoffice\Perimeter\Strategy\ContentTypePerimeterStrategy;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;

/**
 * Class ContentTypePerimeterStrategyTest
 */
class ContentTypePerimeterStrategyTest extends AbstractPerimeterStrategyTest
{
    /**
     * set up the test
     */
    public function setUp()
    {
        $this->strategy = new ContentTypePerimeterStrategy();
        $this->type = ContentTypeInterface::ENTITY_TYPE;
    }

    /**
     * Provide perimeters
     */
    public function providePerimeters()
    {
        $contentType = 'contentType';

        return array(
            'Bad perimeter type : Node' => array($contentType, $this->createPhakeNodePerimeter(), false),
            'Bad perimeter type : Site' => array($contentType, $this->createPhakeSitePerimeter(), false),
            'Bad item : Site id'        => array(2 , $this->createPhakeContentTypePerimeter(), false),
            'Not in perimeter'          => array('otherContentType', $this->createPhakeContentTypePerimeter(), false),
            'In perimeter'              => array($contentType, $this->createPhakeContentTypePerimeter(), true),
        );
    }
}
