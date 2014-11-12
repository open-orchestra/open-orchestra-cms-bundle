<?php

namespace PHPOrchestra\ApiBundle\Test\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use PHPOrchestra\ApiBundle\Facade\AreaFacade;
use PHPOrchestra\ApiBundle\Transformer\NodeTransformer;


/**
 * Class NodeTransformerTest
 */
class NodeTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeTransformer
     */
    protected $nodeTransformer;

    protected $transformerManager;
    protected $encryptionManager;
    protected $siteRepository;
    protected $transformer;
    protected $router;
    protected $node;
    protected $site;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->node = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        $this->site = Phake::mock('PHPOrchestra\ModelBundle\Model\SiteInterface');

        $this->encryptionManager = Phake::mock('PHPOrchestra\BaseBundle\Manager\EncryptionManager');
        $this->siteRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\SiteRepository');
        Phake::when($this->siteRepository)->findOneBySiteId(Phake::anyParameters())->thenReturn($this->site);

        $this->transformer = Phake::mock('PHPOrchestra\ApiBundle\Transformer\BlockTransformer');
        $this->router = Phake::mock('Symfony\Component\Routing\RouterInterface');
        Phake::when($this->router)->generate(Phake::anyParameters())->thenReturn('route');

        $this->transformerManager = Phake::mock('PHPOrchestra\ApiBundle\Transformer\TransformerManager');
        Phake::when($this->transformerManager)->get(Phake::anyParameters())->thenReturn($this->transformer);
        Phake::when($this->transformerManager)->getRouter()->thenReturn($this->router);

        $this->nodeTransformer = new NodeTransformer($this->encryptionManager, $this->siteRepository);

        $this->nodeTransformer->setContext($this->transformerManager);
    }

    /**
     * Test transform
     */
    public function testTransform()
    {
        $facade = Phake::mock('PHPOrchestra\ApiBundle\Facade\FacadeInterface');

        Phake::when($this->transformer)->transform(Phake::anyParameters())->thenReturn($facade);
        $area = Phake::mock('PHPOrchestra\ModelBundle\Model\AreaInterface');
        $areas = new ArrayCollection();
        $areas->add($area);

        Phake::when($this->node)->getAreas()->thenReturn($areas);

        $facade = $this->nodeTransformer->transform($this->node);

        $this->assertInstanceOf('PHPOrchestra\ApiBundle\Facade\NodeFacade', $facade);
        $this->assertArrayHasKey('_self_form', $facade->getLinks());
        $this->assertArrayHasKey('_self_duplicate', $facade->getLinks());
        $this->assertArrayHasKey('_self_version', $facade->getLinks());
        $this->assertArrayHasKey('_self_preview', $facade->getLinks());
        Phake::verify($this->router, Phake::times(7))->generate(Phake::anyParameters());
        Phake::verify($this->transformer)->transform($area, $this->node);
        Phake::verify($this->siteRepository)->findOneBySiteId(Phake::anyParameters());
    }

    /**
     * Test transformVersion
     */
    public function testTransformVersion()
    {
        $facade = $this->nodeTransformer->transformVersion($this->node);

        $this->assertInstanceOf('PHPOrchestra\ApiBundle\Facade\NodeFacade', $facade);
        $this->assertArrayHasKey('_self', $facade->getLinks());
        Phake::verify($this->router)->generate(Phake::anyParameters());
    }
}
