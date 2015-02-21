<?php

namespace OpenOrchestra\BackofficeBundle\FunctionalTest\Controller;

use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;

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

        $this->contentTypeRepository = static::$kernel->getContainer()->get('open_orchestra_model.repository.content_type');
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
