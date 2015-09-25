<?php

namespace OpenOrchestra\ApiBundle\Tests\Functional\Controller;

/**
 * Class AreaControllerTest
 */
class AreaControllerTest extends AbstractControllerTest
{
    /**
     * test reverse transform
     */
    public function testAreaReverseTransform()
    {
        $this->client->request('GET', '/admin/2/homepage/en');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/api/node/root');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $json = json_decode($this->client->getResponse()->getContent(), true);
        $area = $json['areas'][1];
        $this->assertSame('myMain', $area['area_id']);
        if (!array_key_exists(0, $area['areas'])) {
            $this->markTestIncomplete('Datas have been altered');
        }
        $subArea = $area['areas'][0];
        $this->assertSame('mainContentArea1', $subArea['area_id']);
        $block = $subArea['blocks'][0];
        $update = $subArea['links']['_self_block'];


        // Remove ref of area in block
        $formData = json_encode(array('blocks' => array(
        )));

        $this->client->request('POST', $update, array(), array(), array(), $formData);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $language = $this->currentSiteManager->getCurrentSiteDefaultLanguage();
        $siteId = $this->currentSiteManager->getCurrentSiteId();
        $nodeAfter = $this->nodeRepository->findOneByNodeIdAndLanguageAndSiteIdInLastVersion($block['node_id'], $language, $siteId);
        $this->assertSame(array(), $nodeAfter->getBlock(0)->getAreas());

        // Add ref of area in block
        $formData = json_encode(array('blocks' => array(
            array('node_id' => 'root', 'block_id' => 0),
        )));

        $this->client->request('POST', $update, array(), array(), array(), $formData);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
