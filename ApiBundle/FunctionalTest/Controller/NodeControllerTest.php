<?php

namespace PHPOrchestra\ApiBundle\FunctionalTest\Controller;

use PHPOrchestra\ModelInterface\Model\NodeInterface;

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

        $this->client->request('DELETE', '/api/node/fixture_deleted/delete');

        $crawler = $this->client->request('GET', '/admin/');

        $this->assertCount($nbLink - 2, $crawler->filter('a'));
    }

    /**
     * @param $nodes
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
        $nodes = $this->nodeRepository->findByNodeIdAndSiteId('fixture_deleted');
        $this->undeleteNodes($nodes);
        $sons = $this->nodeRepository->findByParentIdAndSiteId('fixture_deleted_son');
        $this->undeleteNodes($sons);

        static::$kernel->getContainer()->get('doctrine.odm.mongodb.document_manager')->flush();
    }

    /**
     * Test node duplicate and references
     */
    public function testDuplicateNode()
    {
        $node = $this->nodeRepository
            ->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion('fixture_full', 'fr', '1');
        $nodeTransverse = $this->nodeRepository
            ->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion(NodeInterface::TRANSVERSE_NODE_ID, 'fr', '1');

        $this->client->request('POST', '/api/node/fixture_full/duplicate?language=fr');

        $nodeLastVersion = $this->nodeRepository
            ->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion('fixture_full', 'fr', '1');

        $nodeRepository = static::$kernel->getContainer()->get('php_orchestra_model.repository.node');
        $nodeTransverseAfter = $nodeRepository
            ->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion(NodeInterface::TRANSVERSE_NODE_ID, 'fr', '1');

        $this->assertSame($node->getVersion()+1, $nodeLastVersion->getVersion());
        $this->assertGreaterThan($this->countAreaRef($nodeTransverse), $this->countAreaRef($nodeTransverseAfter));
    }

    /**
     * Test creation of new language for a node
     */
    public function testCreateNewLanguageNode()
    {
        $node = $this->nodeRepository
            ->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion('fixture_full', 'es', '1');
        if (!is_null($node)) {
            $this->markTestSkipped();
        }

        $nodeTransverse = $this->nodeRepository
            ->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion(NodeInterface::TRANSVERSE_NODE_ID, 'es', '1');
        $countAreaRef = $this->countAreaRef($nodeTransverse);

        $this->assertSame(null, $node);
        $this->assertSame(1, $countAreaRef);

        $this->client->request('GET', '/api/node/fixture_full', array('language' => 'es'));


        $nodeRepository = static::$kernel->getContainer()->get('php_orchestra_model.repository.node');
        $nodeTransverseAfter = $nodeRepository
            ->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion(NodeInterface::TRANSVERSE_NODE_ID, 'es', '1');

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
