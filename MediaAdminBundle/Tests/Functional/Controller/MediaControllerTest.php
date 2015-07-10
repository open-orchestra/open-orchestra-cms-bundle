<?php

namespace OpenOrchestra\MediaAdminBundle\Tests\Functional\Controller;

use OpenOrchestra\BackofficeBundle\Tests\Functional\Controller\AbstractControllerTest;

/**
 * Class MediaControllerTest
 */
class MediaControllerTest extends AbstractControllerTest
{
    protected $media;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();

        $mediaRepository = static::$kernel->getContainer()->get('open_orchestra_media.repository.media');
        $this->media = $mediaRepository->findOneByName('logo Phporchestra');
    }

    /**
     * Test media form
     */
    public function testMediaForm()
    {
        $mediaFolderRepository = static::$kernel->getContainer()->get('open_orchestra_media.repository.media_folder');
        $mediaFolder = $mediaFolderRepository->findOneByName('Images folder');

        $url = '/admin/media/new/' . $mediaFolder->getId();
        $this->client->request('GET', $url);
        $this->assertForm($this->client->getResponse());
    }

    /**
     * @param string $form
     *
     * @dataProvider provideFormType
     */
    public function testMediaForms($form)
    {
        $url = '/admin/media/' . $this->media->getId() . '/' . $form;

        $this->client->request('GET', $url);

        $this->assertForm($this->client->getResponse());
    }

    /**
     * @return array
     */
    public function provideFormType()
    {
        return array(
            array('crop'),
            array('meta'),
        );
    }
}
