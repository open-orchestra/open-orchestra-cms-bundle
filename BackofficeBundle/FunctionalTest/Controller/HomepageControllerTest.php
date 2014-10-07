<?php

namespace PHPOrchestra\BackofficeBundle\FunctionalTest\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class HomepageControllerTest
 */
class HomepageControllerTest extends WebTestCase
{
    /**
     * Test fixture_home
     */
    public function testHomepageWithTree()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = 'nicolas';
        $form['_password'] = 'nicolas';

        $crawler = $client->submit($form);
        $crawler = $client->request('GET', '/admin/');

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
        $this->assertEquals(1, $crawler->filter('a:contains("Home")')->count());

    }

    /**
     * test home page
     */
    public function testHomePageWithTree2()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = 'benjamin';
        $form['_password'] = 'benjamin';

        $crawler = $client->submit($form);
        $crawler = $client->request('GET', '/admin/');
        $crawler = $client->request('GET', '/api/context/site/2/www.bphpOrchestra.fr');
        $crawler = $client->request('GET', '/admin/');

        $this->assertEquals(1, $crawler->filter('html:contains("Editorial")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Administration")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Home")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Orchestra ?")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("A propos")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Communauté")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Contact")')->count());
    }
}
