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
        $this->assertEquals(2, $crawler->filter('a:contains("Home")')->count());

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

        $nodeName = 'fixturetest' . time();
        $formUser['node[name]'] = 'fixturetest' . time();
        $formUser['node[alias]'] = 'page-test';
        $formUser['node[nodeSource]'] = 'root';

        $crawler = $client->submit($formUser);
        $crawler = $client->request('GET', '/admin/');

        $this->assertEquals($nbLink + 2, $crawler->filter('a')->count());

        $crawler = $client->request('GET', '/api/node/' . $nodeName);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $client->getResponse()->headers->get('content-type'));
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
