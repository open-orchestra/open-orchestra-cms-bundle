<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Functional\Controller;

/**
 * Class HomepageControllerTest
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
        $this->assertEquals(1, $crawler->filter('html:contains("Fixture full sample")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Content")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Fixture Home")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Fixture full sample")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Fixture About Us")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Fixture Contact Us")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Fixture Directory")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Fixture Search")')->count());
        $this->assertEquals(2, $crawler->filter('a:contains("Home")')->count());
    }

    /**
     * test new Template
     */
    public function testNewTemplatePageHome()
    {
        $crawler = $this->client->request('GET', '/admin/');
        $nbLink = $crawler->filter('a')->count();

        $crawler = $this->client->request('GET', '/admin/template/new');

        $formUser = $crawler->selectButton('template_submit')->form();
        $formUser['template[name]'] = 'template test ' . time();
        $crawler = $this->client->submit($formUser);

        $crawler = $this->client->request('GET', '/admin/');

        $this->assertEquals($nbLink + 1, $crawler->filter('a')->count());
    }
}
