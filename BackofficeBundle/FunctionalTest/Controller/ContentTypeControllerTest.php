<?php

namespace PHPOrchestra\BackofficeBundle\FunctionalTest\Controller;

use PHPOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;

/**
 * Class ContentTypeControllerTest
 */
class ContentTypeControllerTest extends AbstractControllerTest
{
    /**
     * @var ContentTypeRepositoryInterface
     */
    protected $contentTypeRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();

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
