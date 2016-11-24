<?php

namespace OpenOrchestra\Backoffice\Tests\Perimeter\Strategy;

use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\Backoffice\Perimeter\Strategy\SitePerimeterStrategy;

/**
 * Class SitePerimeterStrategyTest
 */
class SitePerimeterStrategyTest extends AbstractPerimeterStrategyTest
{
    /**
     * set up the test
     */
    public function setUp()
    {
        $this->strategy = new SitePerimeterStrategy();
        $this->type = SiteInterface::ENTITY_TYPE;
    }

    /**
     * Provide perimeters
     */
    public function providePerimeters()
    {
        $siteId = '2';

        return array(
            'Bad perimeter type : Node'         => array($siteId, $this->createPhakeNodePerimeter(), false),
            'Bad perimeter type : Content type' => array($siteId, $this->createPhakeContentTypePerimeter(), false),
            'Bad item : Path'                   => array('root/node1' , $this->createPhakeSitePerimeter(), false),
            'Not in perimeter'                  => array(4, $this->createPhakeSitePerimeter(), false),
            'In perimeter'                      => array($siteId, $this->createPhakeSitePerimeter(), true),
        );
    }
}
