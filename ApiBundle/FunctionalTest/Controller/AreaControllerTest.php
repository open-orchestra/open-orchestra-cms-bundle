<?php

namespace OpenOrchestra\ApiBundle\FunctionalTest\Controller;

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
        $this->client->request('GET', '/admin/');
        $this->client->request('GET', '/api/context/site/1/front.openorchestra.dev');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/api/node/root');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $json = json_decode($this->client->getResponse()->getContent(), true);
        $area = $json['areas'][0];
        $this->assertSame('main', $area['area_id']);
        $block = $area['blocks'][3];
        $update = $area['links']['_self_block'];


        // Remove ref of area in block 3
        $formData = json_encode(array('blocks' => array(
            array('node_id' => 'root', 'block_id' => 0),
            array('node_id' => 'root', 'block_id' => 1),
            array('node_id' => 'root', 'block_id' => 2),
        )));

        $this->client->request('POST', $update, array(), array(), array(), $formData);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $nodeAfter = $this->nodeRepository->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion($block['node_id']);
        $this->assertSame(array(), $nodeAfter->getBlock(3)->getAreas());

        // Add ref of area in block 3
        $formData = json_encode(array('blocks' => array(
            array('node_id' => 'root', 'block_id' => 0),
            array('node_id' => 'root', 'block_id' => 1),
            array('node_id' => 'root', 'block_id' => 2),
            array('node_id' => 'root', 'block_id' => 3),
        )));

        $this->client->request('POST', $update, array(), array(), array(), $formData);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
