<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Manager;

use OpenOrchestra\BackofficeBundle\Manager\TemplateFlexManager;
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
    protected $siteId = 'fakeSiteId';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $templateFlexClass = 'OpenOrchestra\ModelBundle\Document\TemplateFlex';
        $areaClass = 'OpenOrchestra\ModelBundle\Document\AreaFlex';

        $this->contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');
        Phake::when($this->contextManager)->getCurrentSiteId()->thenReturn($this->siteId);

        $this->manager = new TemplateFlexManager(
            $this->contextManager,
            $templateFlexClass,
            $areaClass
        );
    }

    /**
     * Test initialize new template flex
     */
    public function testInitializeNewTemplateFlex()
    {
        $template = $this->manager->initializeNewTemplateFlex();

        $this->assertEquals($template->getSiteId(), $this->siteId);

        $area = $template->getArea();
        $this->assertEquals($area->getLabel(), AreaFlexInterface::ROOT_AREA_LABEL);
        $this->assertEquals($area->getAreaId(), AreaFlexInterface::ROOT_AREA_ID);
        $this->assertEquals($area->getAreaType(), AreaFlexInterface::TYPE_ROOT);
    }
}
