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
            'Bad perimeter type : Node' => array($contentType, $this->createPhakeNodePerimeter(), true),
            'Bad perimeter type : Site' => array($contentType, $this->createPhakeSitePerimeter(), true),
            'Bad item : Site id'        => array(2 , $this->createPhakeContentTypePerimeter(), true),
            'Not in perimeter'          => array('otherContentType', $this->createPhakeContentTypePerimeter(), true),
            'In perimeter'              => array($contentType, $this->createPhakeContentTypePerimeter(), true),
        );
    }
}
