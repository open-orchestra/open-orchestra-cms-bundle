<?php

namespace PHPOrchestra\ApiBundle\FunctionalTest\Controller;

/**
 * Class NodeControllerTest
 */
class NodeControllerTest extends AbstractControllerTest
{
    public function testDeleteAction()
    {
        $this->prepareDatabase();
        $crawler = $this->client->request('GET', '/admin/');
        $nbLink = $crawler->filter('a')->count();

        $crawler = $this->client->request('DELETE', '/api/node/fixture_deleted/delete');

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
}
