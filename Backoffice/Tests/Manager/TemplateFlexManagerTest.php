<?php

namespace OpenOrchestra\Backoffice\Tests\Manager;

use OpenOrchestra\Backoffice\Manager\TemplateFlexManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;
use Phake;

/**
 * Class TemplateFlexManagerTest
 */
class TemplateFlexManagerTest extends AbstractBaseTestCase
{
    /**
     * @var TemplateFlexManager
     */
    protected $manager;
    protected $contextManager;
    protected $areaManager;
    protected $siteId = 'fakeSiteId';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $templateFlexClass = 'OpenOrchestra\ModelBundle\Document\TemplateFlex';
        $areaFlexClass = 'OpenOrchestra\ModelBundle\Document\AreaFlex';

        $rootAreaClass = new $areaFlexClass();
        $rootAreaClass->setAreaType(AreaFlexInterface::TYPE_ROOT);
        $rootAreaClass->setAreaId(AreaFlexInterface::ROOT_AREA_ID);
        $rootAreaClass->setLabel(AreaFlexInterface::ROOT_AREA_LABEL);

        $this->contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');
        $this->areaManager = Phake::mock('OpenOrchestra\Backoffice\Manager\AreaFlexManager');
        Phake::when($this->contextManager)->getCurrentSiteId()->thenReturn($this->siteId);
        Phake::when($this->areaManager)->initializeNewAreaRoot()->thenReturn($rootAreaClass);

        $this->manager = new TemplateFlexManager(
            $this->contextManager,
            $templateFlexClass,
            $this->areaManager
        );
    }

    /**
     * Test initialize new template flex
     */
    public function testInitializeNewTemplateFlex()
    {
        $template = $this->manager->initializeNewTemplateFlex();

        Phake::verify($this->areaManager)->initializeNewAreaRoot();
        $this->assertEquals($template->getSiteId(), $this->siteId);

        $area = $template->getArea();
        $this->assertEquals($area->getLabel(), AreaFlexInterface::ROOT_AREA_LABEL);
        $this->assertEquals($area->getAreaId(), AreaFlexInterface::ROOT_AREA_ID);
        $this->assertEquals($area->getAreaType(), AreaFlexInterface::TYPE_ROOT);
    }
}
