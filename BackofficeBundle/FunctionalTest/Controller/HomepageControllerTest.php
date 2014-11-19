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
        $this->markTestSkipped();
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

    /**
     * test new Node
     */
    public function testNewNodePageHome()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = 'benjamin';
        $form['_password'] = 'benjamin';

        $crawler = $client->submit($form);
        $crawler = $client->request('GET', '/admin/');

        $nbLink = $crawler->filter('a')->count();

        $crawler = $client->request('GET', '/admin/node/new/fixture_full');

        $formUser = $crawler->selectButton('node_submit')->form();

        $formUser['node[name]'] = 'fixture test ' . time();
        $formUser['node[alias]'] = 'page-test';
        $formUser['node[nodeSource]'] = 'root';

        $crawler = $client->submit($formUser);
        $crawler = $client->request('GET', '/admin/');

        $this->assertEquals($nbLink + 2, $crawler->filter('a')->count());
    }

    /**
     * test new Template
     */
    public function testNewTemplatePageHome()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = 'benjamin';
        $form['_password'] = 'benjamin';

        $crawler = $client->submit($form);
        $crawler = $client->request('GET', '/admin/');

        $nbLink = $crawler->filter('a')->count();

        $crawler = $client->request('GET', '/admin/template/form');

        $formUser = $crawler->selectButton('template_submit')->form();

        $formUser['template[name]'] = 'template test ' . time();

        $crawler = $client->submit($formUser);
        $crawler = $client->request('GET', '/admin/');

        $this->assertEquals($nbLink + 1, $crawler->filter('a')->count());
    }
}
