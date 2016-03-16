<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Functional\Controller;

/**
 * Class HomepageControllerTest
 *
 * @group backofficeTest
 */
class HomepageControllerTest extends AbstractControllerTest
{
    /**
     * Test fixture_home
     */
    public function testHomepageWithTree()
    {
        $crawler = $this->client->request('GET', '/admin/');

        $this->assertEquals(1, $crawler->filter('html:contains("Editorial")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Administration")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Orchestra ?")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Content")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Orchestra ?")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Communauté")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Actualité")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Mentions Légales")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Home")')->count());
    }

    /**
     * test new Template
     */
    public function testNewTemplatePageHome()
    {
        $crawler = $this->client->request('GET', '/admin/');
        $nbLink = $crawler->filter('a')->count();

        $crawler = $this->client->request('GET', '/admin/template/new');

        $formUser = $crawler->selectButton('Save')->form();
        $formUser['oo_template[name]'] = 'template test ' . time();
        $this->client->submit($formUser);

        $crawler = $this->client->request('GET', '/admin/');

        $this->assertEquals($nbLink + 1, $crawler->filter('a')->count());
    }
}
