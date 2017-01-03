<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\ApiBundle\Facade\BlockFacade;
use OpenOrchestra\ApiBundle\Transformer\AreaTransformer;

/**
 * Class AreaTransformerTest
 */
class AreaTransformerTest extends AbstractBaseTestCase
{
    /**
     * @var AreaTransformer
     */
    protected $areaTransformer;

    protected $facadeClass = 'OpenOrchestra\ApiBundle\Facade\AreaFacade';
    protected $transformerManager;
    protected $transformer;
    protected $block;
    protected $area;
    protected $authorizationChecker;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        $this->area = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($this->area)->getBlocks()->thenReturn(array($this->block, $this->block, $this->block));

        $this->transformer = Phake::mock('OpenOrchestra\ApiBundle\Transformer\BlockTransformer');

        $this->transformerManager = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        Phake::when($this->transformerManager)->get(Phake::anyParameters())->thenReturn($this->transformer);

        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn(true);

        $this->areaTransformer = new AreaTransformer(
            $this->facadeClass,
            $this->authorizationChecker
        );

        $this->areaTransformer->setContext($this->transformerManager);
    }

    /**
     * test transform
     */
    public function testTransform()
    {
        $blockFacade = new BlockFacade();
        Phake::when($this->transformer)->transform(Phake::anyParameters())->thenReturn($blockFacade);

        $areaFacade = $this->areaTransformer->transform($this->area);

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\AreaFacade', $areaFacade);
        Phake::verify($this->transformer, Phake::times(3))->transform($this->block);
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $this->assertSame('area', $this->areaTransformer->getName());
    }
}
