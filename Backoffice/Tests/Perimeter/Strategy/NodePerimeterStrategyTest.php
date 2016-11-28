<?php

namespace OpenOrchestra\Backoffice\Tests\Perimeter\Strategy;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\Backoffice\Perimeter\Strategy\NodePerimeterStrategy;

/**
 * Class NodePerimeterStrategyTest
 */
class NodePerimeterStrategyTest extends AbstractPerimeterStrategyTest
{
    /**
     * set up the test
     */
    public function setUp()
    {
        $this->strategy = new NodePerimeterStrategy();
        $this->type = NodeInterface::ENTITY_TYPE;
    }

    /**
     * Provide perimeters
     */
    public function providePerimeters()
    {
        $path = '/root/node1/node2/';

        return array(
            'Bad perimeter type : Content type' => array($path, $this->createPhakeContentTypePerimeter(), false),
            'Bad perimeter type : Site'         => array($path, $this->createPhakeSitePerimeter(), false),
            'Bad item : Site id'                => array(2 , $this->createPhakeNodePerimeter(), false),
            'Not in perimeter'                  => array('/root/node3/node4', $this->createPhakeNodePerimeter(), false),
            'In perimeter'                      => array($path, $this->createPhakeNodePerimeter(), true),
        );
    }
}
