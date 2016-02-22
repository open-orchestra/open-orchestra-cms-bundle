<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;
use Phake;
use OpenOrchestra\ApiBundle\Transformer\AreaFlexTransformer;

/**
 * Class AreaFlexTransformerTest
 */
class AreaFlexTransformerTest extends AbstractBaseTestCase
{
    /**
     * @var AreaFlexTransformer
     */
    protected $areaTransformer;

    protected $facadeClass = 'OpenOrchestra\ApiBundle\Facade\AreaFacade';
    protected $authorizationChecker;
    protected $transformerManager;
    protected $router;

    /**
     * Set up the test
     */
    public function setUp()
    {

        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn(true);

        $this->router = Phake::mock('Symfony\Component\Routing\RouterInterface');
        Phake::when($this->router)->generate(Phake::anyParameters())->thenReturn('route');
        $this->transformerManager = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        Phake::when($this->transformerManager)->getRouter()->thenReturn($this->router);

        $this->areaTransformer = new AreaFlexTransformer($this->facadeClass, $this->authorizationChecker);
        $this->areaTransformer->setContext($this->transformerManager);
    }

    /**
     * @param string $areaType
     * @param int    $countGenerate
     *
     * @dataProvider provideAreaTypeAndCountGenerate
     */
    public function testTransformFromTemplateFlexGenerateUrl($areaType, $countGenerate)
    {
        $template = Phake::mock('OpenOrchestra\ModelInterface\Model\TemplateFlexInterface');
        Phake::when($template)->getTemplateId()->thenReturn('templateId');

        $area = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaFlexInterface');
        Phake::when($area)->getLabel()->thenReturn('label');
        Phake::when($area)->getAreaId()->thenReturn('areaId');
        Phake::when($area)->getAreaType()->thenReturn($areaType);
        Phake::when($area)->getWidth()->thenReturn('100px');
        Phake::when($area)->getAreas()->thenReturn(new ArrayCollection());

        $parentAreaId = 'fakeParentAreaId';

        $areaFacade = $this->areaTransformer->transformFromTemplateFlex($area, $template, $parentAreaId);

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\AreaFacade', $areaFacade);
        Phake::verify($this->router, Phake::times($countGenerate))->generate(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideAreaTypeAndCountGenerate()
    {
        return array(
            array(AreaFlexInterface::TYPE_ROW, 2),
            array(AreaFlexInterface::TYPE_ROOT, 2),
            array(AreaFlexInterface::TYPE_COLUMN, 6)
        );
    }

    /**
     * Test reverse transform order
     */
    public function testReserveTransform()
    {
        $subAreaId1 = 'fake_areaId1';
        $subAreaId2 = 'fake_areaId2';
        $subArea = array();
        $subArea1 = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaFlexInterface');
        Phake::when($subArea1)->getAreaId()->thenReturn($subAreaId1);
        $subArea2 = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaFlexInterface');
        Phake::when($subArea2)->getAreaId()->thenReturn($subAreaId2);
        $subArea[] = $subArea1;
        $subArea[] = $subArea2;

        $source = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaFlexInterface');
        Phake::when($source)->getAreas()->thenReturn($subArea);

        $subAreaFacade = array();
        $subAreaFacade1 = Phake::mock('OpenOrchestra\BaseApi\Facade\FacadeInterface');
        $subAreaFacade1->areaId = $subAreaId1;
        $subAreaFacade1->order = 1;

        $subAreaFacade2 = Phake::mock('OpenOrchestra\BaseApi\Facade\FacadeInterface');
        $subAreaFacade2->areaId = $subAreaId2;
        $subAreaFacade2->order = 0;
        $subAreaFacade[] = $subAreaFacade1;
        $subAreaFacade[] = $subAreaFacade2;

        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\AreaFlexFacade');
        Phake::when($facade)->getAreas()->thenReturn($subAreaFacade);

        $this->areaTransformer->reverseTransform($facade, $source);
        Phake::verify($source)->setAreas(Phake::capture($subAreaUpdated));

        $this->assertEquals($subAreaId1, $subAreaUpdated[1]->getAreaId());
        $this->assertEquals($subAreaId2, $subAreaUpdated[0]->getAreaId());
    }

    /**
     * Test exception reverse transform
     */
    public function testExceptionReverseTransform()
    {
        $facade = Phake::mock('OpenOrchestra\BaseApi\Facade\FacadeInterface');
        $this->setExpectedException('\UnexpectedValueException');
        $this->areaTransformer->reverseTransform($facade);
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $this->assertSame('area_flex', $this->areaTransformer->getName());
    }
}
