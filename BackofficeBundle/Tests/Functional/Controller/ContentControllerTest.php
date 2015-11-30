<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Functional\Controller;

/**
 * Class ContentControllerTest
 */
class ContentControllerTest extends AbstractControllerTest
{
    /**
     * Test content edition
     */
    public function testEditContent()
    {
        $url = '/admin/content/form/welcome?language=fr';
        $crawler = $this->client->request('GET', $url);
        $this->assertNotContains('has-error', $this->client->getResponse()->getContent());
        $contentForm = $crawler->selectButton('Save')->form();
        $this->client->submit($contentForm);
        $this->assertContains('alert alert-success', $this->client->getResponse()->getContent());
    }

    /**
     * Test new content
     */
    public function testNewContent()
    {
        $url = '/admin/content/new/news';
        $this->client->request('GET', $url);
        $this->assertNotContains('has-error', $this->client->getResponse()->getContent());
    }
}
