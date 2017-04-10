<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type\Component;

use OpenOrchestra\Backoffice\GeneratePerimeter\Strategy\NodeGeneratePerimeterStrategy;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class NodeGeneratePerimeterStrategyTest
 */
class NodeGeneratePerimeterStrategyTest extends AbstractBaseTestCase
{
    protected $strategy;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $context = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');

        $repository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        Phake::when($repository)->findTreeNode(Phake::anyParameters())->thenReturn(array(
            array(
                'node' => array(
                    'path' => 'root',
                    'name' => 'Orchestra ?',
                ),
                'child' =>
                array(
                    array(
                        'node' =>
                        array(
                            'path' => 'root/fixture_page_community',
                            'name' => 'Communauté',
                        ),
                        'child' => array(),
                    ),
                    array(
                        'node' =>
                        array(
                            'path' => 'root/fixture_page_news',
                            'name' => 'Actualités',
                        ),
                        'child' => array(),
                    ),
                    array(
                        'node' =>
                        array(
                            'path' => 'root/fixture_page_contact',
                            'name' => 'Contact',
                        ),
                        'child' =>array(),
                    ),
                    array(
                        'node' =>
                        array(
                            'path' => 'root/fixture_page_legal_mentions',
                            'name' => 'Mentions Légales',
                        ),
                        'child' => array(),
                    ),
                    array (
                        'node' =>
                        array (
                            'path' => 'root/fixture_auto_unpublish',
                            'name' => 'Dépublication auto',
                        ),
                        'child' => array(),
                    ),
                ),
            ),
        ));


        $this->strategy = new NodeGeneratePerimeterStrategy($repository, $context);
    }

    /**
     * Test getPerimeterConfiguration
     */
    public function testGetPerimeterConfiguration()
    {
        $result = $this->strategy->getPerimeterConfiguration('siteId');

        $this->assertEquals(array(
            array(
                'root' => array(
                    'path' => 'root',
                    'name' => 'Orchestra ?',
                ),
                'children' =>
                array(
                    array(
                        'root' =>
                        array(
                            'path' => 'root/fixture_page_community',
                            'name' => 'Communauté',
                        )
                    ),
                    array(
                        'root' =>
                        array(
                            'path' => 'root/fixture_page_news',
                            'name' => 'Actualités',
                        )
                    ),
                    array(
                        'root' =>
                        array(
                            'path' => 'root/fixture_page_contact',
                            'name' => 'Contact',
                        )
                    ),
                    array(
                        'root' =>
                        array(
                            'path' => 'root/fixture_page_legal_mentions',
                            'name' => 'Mentions Légales',
                        )
                    ),
                    array (
                        'root' =>
                        array (
                            'path' => 'root/fixture_auto_unpublish',
                            'name' => 'Dépublication auto',
                        )
                    ),
                ),
            )
        ), $result);
    }

    /**
     * Test generatePerimeter
     */
    public function testGeneratePerimeter()
    {
        $result = $this->strategy->generatePerimeter('siteId');
        $this->assertEquals(array(
            'root',
            'root/fixture_page_community',
            'root/fixture_page_news',
            'root/fixture_page_contact',
            'root/fixture_page_legal_mentions',
            'root/fixture_auto_unpublish',
        ), $result);
    }

    /**
     * Test getType
     */
    public function testGetType()
    {
        $result = $this->strategy->getType();
        $this->assertEquals(NodeInterface::ENTITY_TYPE, $result);
    }
}
