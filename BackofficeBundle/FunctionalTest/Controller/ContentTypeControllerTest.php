<?php

namespace PHPOrchestra\BackofficeBundle\FunctionalTest\Controller;

use PHPOrchestra\ModelBundle\Repository\ContentTypeRepository;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ContentTypeControllerTest
 */
class ContentTypeControllerTest extends WebTestCase
{
    /**
     * @var ContentTypeRepository
     */
    protected $contentTypeRepository;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->client = static::createClient();
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = 'nicolas';
        $form['_password'] = 'nicolas';

        $crawler = $this->client->submit($form);

        $this->contentTypeRepository = static::$kernel->getContainer()->get('php_orchestra_model.repository.content_type');
    }

    /**
     * Test content type versionnning
     */
    public function testFormController()
    {
        $contentTypes = $this->contentTypeRepository->findAll();
        $contentTypeCount = count($contentTypes);

        $crawler = $this->client->request('GET', '/admin/content-type/form/car');
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form);

        $contentTypes = $this->contentTypeRepository->findAll();
        $this->assertCount($contentTypeCount + 1, $contentTypes);
    }
}
