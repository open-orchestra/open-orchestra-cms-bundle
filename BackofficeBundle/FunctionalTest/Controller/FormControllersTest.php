<?php

namespace PHPOrchestra\BackofficeBundle\FunctionalTest\Controller;

/**
 * Class FormControllersTest
 */
class FormControllersTest extends AbstractControllerTest
{
    /**
     * @param string $url
     *
     * @dataProvider provideApiUrl
     */
    public function testForm($url)
    {
        $crawler = $this->client->request('GET', $url);

        $this->assertForm($this->client->getResponse());
    }

    /**
     * @return array
     */
    public function provideApiUrl()
    {
        return array(
            array('/admin/site/form/1'),
            array('/admin/status/new'),
            array('/admin/theme/new'),
            array('/admin/keyword/new'),
            array('/admin/template/form/template_full'),
            array('/admin/template/area/form/template_full/left_menu'),
            array('/admin/content-type/form/car'),
            array('/admin/content-type/new'),
        );
    }

    /**
     * Test content form
     */
    public function testContentForm()
    {
        $contentRepository = static::$kernel->getContainer()->get('php_orchestra_model.repository.content');
        $content = $contentRepository->findOneByName('Welcome');

        $url = '/admin/content/form/' . $content->getId();
        $this->client->request('GET', $url);
        $this->assertForm($this->client->getResponse());
    }

    /**
     * Test folder form
     */
    public function testMediaFolderForm()
    {
        $mediaFolderRepository = static::$kernel->getContainer()->get('php_orchestra_media.repository.media_folder');
        $mediaFolder = $mediaFolderRepository->findOneByName('Images folder');

        $url = '/admin/folder/form/' . $mediaFolder->getId();
        $this->client->request('GET', $url);
        $this->assertForm($this->client->getResponse());
    }
}
