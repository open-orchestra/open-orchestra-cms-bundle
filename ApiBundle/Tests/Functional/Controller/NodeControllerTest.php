<?php

namespace OpenOrchestra\ApiBundle\Tests\Functional\Controller;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;

/**
 * Class NodeControllerTest
 */
class NodeControllerTest extends AbstractControllerTest
{
    /**
     * @var StatusRepositoryInterface
     */
    protected $statusRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $this->statusRepository = static::$kernel->getContainer()->get('open_orchestra_model.repository.status');
    }

    /**
     * Reset removing node after test
     */
    public function tearDown()
    {
        $nodes = $this->nodeRepository->findByNodeIdAndSiteId('fixture_page_contact', '2');
        $this->undeleteNodes($nodes);
        static::$kernel->getContainer()->get('object_manager')->flush();
    }

    /**
     * Test delete action
     */
    public function testDeleteAction()
    {
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
        $this->assertGreaterThanOrEqual($this->countAreaRef($nodeTransverse), $this->countAreaRef($nodeTransverseAfter));
    }

    /**
     * Test creation of new language for a node
     */
    public function testCreateNewLanguageNode()
    {
        $node = $this->nodeRepository
            ->findOneByNodeIdAndLanguageAndSiteIdInLastVersion('root', 'en', '2');
        if (!is_null($node)) {
            $this->markTestIncomplete('The node has already been created');
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

    /**
     * @param string $name
     * @param int    $publishedVersion
     *
     * @dataProvider provideStatusNameAndPublishedVersion
     */
    public function testChangeNodeStatus($name, $publishedVersion)
    {
        $node = $this->nodeRepository->findOneByNodeIdAndLanguageAndSiteIdInLastVersion('root', 'fr', '2');
        $newStatus = $this->statusRepository->findOneByName($name);
        $newStatusId = $newStatus->getId();

        $this->client->request(
            'POST',
            '/api/node/' . $node->getId() . '/update',
            array(),
            array(),
            array(),
            json_encode(array('status_id' => $newStatusId))
        );

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $newNode = $this->nodeRepository->findOnePublishedByNodeIdAndLanguageAndSiteIdInLastVersion('root', 'fr', '2');
        $this->assertEquals($publishedVersion, $newNode->getVersion());
    }

    /**
     * @return array
     */
    public function provideStatusNameAndPublishedVersion()
    {
        return array(
            array('pending', 1),
            array('published', 2),
            array('draft', 1),
        );
    }
}
