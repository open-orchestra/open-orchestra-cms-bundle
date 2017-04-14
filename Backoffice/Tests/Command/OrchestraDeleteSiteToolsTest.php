<?php

namespace OpenOrchestra\BackOffice\Tests\Command\Strategies;

use OpenOrchestra\Backoffice\Command\OrchestraDeleteSiteTools;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Phake;

/**
 * @package OpenOrchestra\BackOffice\Tests\Command\Strategies
 */
class OrchestraDeleteSiteToolsTest extends AbstractBaseTestCase
{
    /**
     * @var OrchestraDeleteSiteTools
     */
    protected $deleteSiteTools;
    protected $contentRepository;
    protected $blockRepository;
    protected $objectManager;
    protected $referenceManager;
    protected $io;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->contentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');
        $this->blockRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\BlockRepositoryInterface');
        $this->objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        $this->referenceManager = Phake::mock('OpenOrchestra\Backoffice\Reference\ReferenceManager');
        $this->io = Phake::mock('Symfony\Component\Console\Style\SymfonyStyle');

        $this->deleteSiteTools = new OrchestraDeleteSiteTools(
            $this->contentRepository,
            $this->blockRepository,
            $this->objectManager,
            $this->referenceManager
        );
    }

    /**
     * Test findUsageReferenceInOtherSite
     */
    public function testFindUsageReferenceInOtherSite()
    {
        $siteId = 'fakeSiteId';
        $blockId = 'blockId';
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        $block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        $block2 = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($block)->getSiteId()->thenReturn('otherSiteId');
        Phake::when($block)->getId()->thenReturn($blockId);
        Phake::when($block2)->getSiteId()->thenReturn($siteId);
        Phake::when($content)->getUseReferences()->thenReturn(
            array(
                BlockInterface::ENTITY_TYPE => array($block, $block2)
            )
        );
        Phake::when($this->blockRepository)->findById(Phake::anyParameters())->thenReturn($block);

        $entities = array($content);

        $usedOtherSite = $this->deleteSiteTools->findUsageReferenceInOtherSite($siteId, $entities);
        $this->assertEquals(array(
            array(
                'entity' => $content,
                'references' => array(
                    'block' => array($blockId => $block)
                )
            )
        ), $usedOtherSite);
    }

    /**
     * test display used references
     */
    public function testDisplayUsedReferences()
    {
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        $contentName = 'contentName';
        Phake::when($content)->getName()->thenReturn($contentName);
        $blockLabel = 'blockLabel';
        $blockLanguage = 'blockLanguage';
        $blockType = 'blockType';
        $siteId = 'fakeSiteId';
        $block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($block)->getLabel()->thenReturn($blockLabel);
        Phake::when($block)->getLanguage()->thenReturn($blockLanguage);
        Phake::when($block)->getComponent()->thenReturn($blockType);
        Phake::when($block)->getSiteId()->thenReturn($siteId);

        $usedReferences = array(
            array(
                'entity' => $content,
                'references' => array(
                    'block' => array($block)
                )
            )
        );

        $this->deleteSiteTools->displayUsedReferences($this->io, $usedReferences);
        Phake::verify($this->io)->comment('Entity <info>' . $contentName . ' is used in :');
        Phake::verify($this->io)->newLine();
        Phake::verify($this->io)->text('-----------------------------------------------------------');
        Phake::verify($this->io)->text('    <comment>Blocks:</comment>');
        Phake::verify($this->io)->text('    *  Name: <info>' . $blockLabel . '</info> Language: <info>' . $blockLanguage . '</info> Type <info>' . $blockType . '</info> in site <info>' . $siteId . '</info>');
    }
}
