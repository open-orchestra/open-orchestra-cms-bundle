<?php

namespace OpenOrchestra\Backoffice\Tests\Perimeter\Strategy;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\Backoffice\Model\PerimeterInterface;
use Phake;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;

/**
 * Class AbstractPerimeterStrategyTest
 */
abstract class AbstractPerimeterStrategyTest extends AbstractBaseTestCase
{
    protected $strategy;
    protected $type;

    /**
     * Test getType
     */
    public function testGetType()
    {
        $this->assertSame($this->type, $this->strategy->getType());
    }

    /**
     * Test isInPerimeter
     *
     * @param mixed              $item
     * @param PerimeterInterface $perimeter
     * @param bool               $inPerimeter
     *
     * @dataProvider providePerimeters
     */
    public function testIsInPerimeter($item, PerimeterInterface $perimeter, $inPerimeter)
    {
        $this->assertSame($inPerimeter, $this->strategy->isInPerimeter($item, $perimeter));
    }

    /**
     * Provide perimeters
     */
    abstract public function providePerimeters();

    /**
     * Create a phake NodePerimeter
     *
     * @return PerimeterInterface
     */
    protected function createPhakeNodePerimeter()
    {
        $perimeter = Phake::mock('OpenOrchestra\Backoffice\Model\PerimeterInterface');
        Phake::when($perimeter)->getType()->thenReturn(NodeInterface::ENTITY_TYPE);
        $items = array('/root/node1/', '/root/node2/');
        Phake::when($perimeter)->getItems()->thenReturn($items);

        return $perimeter;
    }

    /**
     * Create a phake ContentTypePerimeter
     *
     * @return PerimeterInterface
     */
    protected function createPhakeContentTypePerimeter()
    {
        $perimeter = Phake::mock('OpenOrchestra\Backoffice\Model\PerimeterInterface');
        Phake::when($perimeter)->getType()->thenReturn(ContentTypeInterface::ENTITY_TYPE);
        $items = array('contentType', 'anotherContentType');
        Phake::when($perimeter)->getItems()->thenReturn($items);

        return $perimeter;
    }

    /**
     * Create a phake SitePerimeter
     *
     * @return PerimeterInterface
     */
    protected function createPhakeSitePerimeter()
    {
        $perimeter = Phake::mock('OpenOrchestra\Backoffice\Model\PerimeterInterface');
        Phake::when($perimeter)->getType()->thenReturn(SiteInterface::ENTITY_TYPE);
        $items = array(2, 3);
        Phake::when($perimeter)->getItems()->thenReturn($items);

        return $perimeter;
    }
}
