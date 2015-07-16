<?php

namespace OpenOrchestra\ApiBundle\Tests\Functional\Controller;

use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class NodeControllerTest
 */
class NodeControllerTest extends AbstractControllerTest
{
    /**
     * Test delete action
     */
    public function testDeleteAction()
    {
        $this->prepareDatabase();
        $crawler = $this->client->request('GET', '/admin/');
        $nbLink = $crawler->filter('a')->count();

        $this->client->request('DELETE', '/api/node/fixture_page_contact/delete');

        $crawler = $this->client->request('GET', '/admin/');

        $this->assertCount($nbLink - 2, $crawler->filter('a'));
    }

    /**
     * @param array $nodes
     */
    protected function undeleteNodes($nodes)
    {
        foreach ($nodes as $node) {
            $node->setDeleted(false);
        }
    }

    /**
     * @return array
     */
    protected function prepareDatabase()
    {
        $nodes = $this->nodeRepository->findByNodeIdAndSiteId('fixture_page_contact', '2');
        $this->undeleteNodes($nodes);

        static::$kernel->getContainer()->get('doctrine.odm.mongodb.document_manager')->flush();
    }

    /**
     * Test node duplicate and references
     */
    public function testDuplicateNode()
    {
        $node = $this->nodeRepository
            ->findOneByNodeIdAndLanguageAndSiteIdInLastVersion('fixture_page_community', 'fr', '2');
        $nodeTransverse = $this->nodeRepository
            ->findOneByNodeIdAndLanguageAndSiteIdInLastVersion(NodeInterface::TRANSVERSE_NODE_ID, 'fr', '2');

        $this->client->request('POST', '/api/node/fixture_page_community/duplicate?language=fr');

        $nodeLastVersion = $this->nodeRepository
            ->findOneByNodeIdAndLanguageAndSiteIdInLastVersion('fixture_page_community', 'fr', '2');

        $nodeRepository = static::$kernel->getContainer()->get('open_orchestra_model.repository.node');
        $nodeTransverseAfter = $nodeRepository
            ->findOneByNodeIdAndLanguageAndSiteIdInLastVersion(NodeInterface::TRANSVERSE_NODE_ID, 'fr', '2');

        $this->assertSame($node->getVersion()+1, $nodeLastVersion->getVersion());
        $this->assertGreaterThan($this->countAreaRef($nodeTransverse), $this->countAreaRef($nodeTransverseAfter));
    }

    /**
     * Test creation of new language for a node
     */
    public function testCreateNewLanguageNode()
    {
        $node = $this->nodeRepository
            ->findOneByNodeIdAndLanguageAndSiteIdInLastVersion('root', 'en', '2');
        if (!is_null($node)) {
            $this->markTestSkipped();
        }

        $nodeTransverse = $this->nodeRepository
            ->findOneByNodeIdAndLanguageAndSiteIdInLastVersion(NodeInterface::TRANSVERSE_NODE_ID, 'en', '2');
        $countAreaRef = $this->countAreaRef($nodeTransverse);

        $this->assertSame(null, $node);
        $this->assertSame(5, $countAreaRef);

        $this->client->request('GET', '/api/node/root/show-or-create', array('language' => 'en'));


        $nodeRepository = static::$kernel->getContainer()->get('open_orchestra_model.repository.node');
        $nodeTransverseAfter = $nodeRepository
            ->findOneByNodeIdAndLanguageAndSiteIdInLastVersion(NodeInterface::TRANSVERSE_NODE_ID, 'en', '2');

        $this->assertGreaterThan($countAreaRef, $this->countAreaRef($nodeTransverseAfter));
    }

    /**
     * @param NodeInterface $node
     *
     * @return int
     */
    public function countAreaRef(NodeInterface $node)
    {
        $areaRef = 0;
        foreach ($node->getBlocks() as $block) {
            $areaRef = $areaRef + count($block->getAreas());
        }

        return $areaRef;
    }
}
