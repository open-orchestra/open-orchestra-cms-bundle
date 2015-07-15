<?php

namespace OpenOrchestra\MediaAdminBundle\Tests\Functional\Controller;

use OpenOrchestra\BackofficeBundle\Tests\Functional\Controller\AbstractControllerTest;

/**
 * Class FolderControllerTest
 */
class FolderControllerTest extends AbstractControllerTest
{
    /**
     * Test folder form
     */
    public function testMediaFolderForm()
    {
        $mediaFolderRepository = static::$kernel->getContainer()->get('open_orchestra_media.repository.media_folder');
        $mediaFolder = $mediaFolderRepository->findOneByName('Images folder');

        $url = '/admin/folder/form/' . $mediaFolder->getId();
        $crawler = $this->client->request('GET', $url);
        $this->assertForm($this->client->getResponse());

        $form = $crawler->selectButton('Save')->form();

        $this->client->submit($form);
        $this->assertForm($this->client->getResponse());
    }
}
