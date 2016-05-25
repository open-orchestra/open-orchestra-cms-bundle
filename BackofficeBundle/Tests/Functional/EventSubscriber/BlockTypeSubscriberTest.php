<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Functional\EventSubscriber;

use OpenOrchestra\BackofficeBundle\Tests\Functional\AbstractAuthentificatedTest;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ConfigurableContentStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ContentListStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\VideoStrategy;
use OpenOrchestra\Media\DisplayBlock\Strategies\GalleryStrategy;
use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormFactoryInterface;
use OpenOrchestra\ModelInterface\Repository\ReadContentRepositoryInterface;

/**
 * Class BlockTypeSubscriberTest
 *
 * @group backofficeTest
 */
class BlockTypeSubscriberTest extends AbstractAuthentificatedTest
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;
    protected $keywords;
    protected $keywordsLabelToId;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $keywordRepository = static::$kernel->getContainer()->get('open_orchestra_model.repository.keyword');
        $keywords = $keywordRepository->findAll();
        $this->keywordsLabelToId = array();
        foreach($keywords as $keywords) {
            $this->keywordsLabelToId['##' . $keywords->getLabel() . '##'] = '##' . $keywords->getId() . '##';
        }
        $this->formFactory = static::$kernel->getContainer()->get('form.factory');
    }

    /**
     * Test video block : checkbox unique to uncheck
     */
    public function testVideoBlock()
    {
        $block = new Block();
        $block->setComponent(VideoStrategy::NAME);
        $block->addAttribute('videoType', 'youtube');
        $block->addAttribute('youtubeFs', true);

        $form = $this->formFactory->create('oo_block', $block, array('csrf_protection' => false));

        $form->submit(array(
            'id' => 'testId',
            'class' => 'testClass',
            'videoType' => 'youtube',
            'youtubeVideoId' => 'videoId',
            'youtubeAutoplay' => '1',
        ));

        $this->assertTrue($form->isSynchronized());
        /** @var BlockInterface $data */
        $data = $form->getConfig()->getData();
        $this->assertBlock($data);
        $this->assertSame('videoId', $data->getAttribute('youtubeVideoId'));
        $this->assertTrue($data->getAttribute('youtubeAutoplay'));
        $this->assertNull($data->getAttribute('youtubeFs'));
    }

    /**
     * @param string $component
     * @param array  $value
     *
     * @dataProvider provideComponentAndData
     */
    public function testMultipleBlock($component, $value)
    {
        $block = new Block();
        $block->setComponent($component);

        $form = $this->formFactory->create('oo_block', $block, array('csrf_protection' => false));

        $submittedValue = array_merge(array('id' => 'testId', 'class' => 'testClass'), $value);
        $form->submit($submittedValue);

        $this->assertTrue($form->isSynchronized());
        /** @var BlockInterface $data */
        $data = $form->getConfig()->getData();
        $this->assertBlock($data);
        foreach ($value as $key => $sendData) {
            $this->assertSame($sendData, $data->getAttribute($key));
        }
    }

    /**
     * @return array
     */
    public function provideComponentAndData()
    {
        return array(
            array(GalleryStrategy::NAME, array(
                'pictures' => array(
                    'media1',
                    'media2'
                )
            )),
            array(ContentListStrategy::NAME, array(
                'contentNodeId' => 'news',
                'contentTemplateEnabled' => true,
            )),
            array(ConfigurableContentStrategy::NAME, array(
                'contentSearch' => array(
                    'contentType' => 'car',
                    'keywords' => '',
                    'choiceType' => ReadContentRepositoryInterface::CHOICE_AND,
                ),
                'contentTemplateEnabled' => true,
            ))
        );
    }

    /**
     * @param string $component
     * @param array  $value
     *
     * @dataProvider provideComponentAndDataAndTransformedValue
     */
    public function testMultipleBlockWithDataTransformation($component, $value, $transformedValue)
    {
        $block = new Block();
        $block->setComponent($component);
        $transformedValue = $this->replaceKeywordLabelById($transformedValue);
        $form = $this->formFactory->create('oo_block', $block, array('csrf_protection' => false));

        $submittedValue = array_merge(array('id' => 'testId', 'class' => 'testClass'), $value);
        $form->submit($submittedValue);

        $this->assertTrue($form->isSynchronized());
        /** @var BlockInterface $data */
        $data = $form->getConfig()->getData();
        $this->assertBlock($data);
        foreach ($transformedValue as $key => $receivedData) {
            $this->assertSame($receivedData, $data->getAttribute($key));
        }
    }

    /**
     * @return array
     */
    public function provideComponentAndDataAndTransformedValue()
    {
        return array(
                array(ContentListStrategy::NAME, array(
                        'contentNodeId' => 'news',
                        'contentTemplateEnabled' => true,
                        'contentSearch' => array(
                                'keywords' => 'lorem AND ipsum',
                            )
                ), array(
                        'contentNodeId' => 'news',
                        'contentTemplateEnabled' => true,
                        'contentSearch' => array(
                                'keywords' => '{"$and":[{"keywords":{"$eq":"##lorem##"}},{"keywords":{"$eq":"##ipsum##"}}]}'
                            )
                    )),
        );
    }

    /**
     * @param $data
     */
    protected function assertBlock($data)
    {
        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\BlockInterface', $data);
        $this->assertSame('testId', $data->getId());
        $this->assertSame('testClass', $data->getClass());
    }

    /**
     * @param array $data
     */
    protected function replaceKeywordLabelById($data)
    {
        $keywordsLabelToId = $this->keywordsLabelToId;
        array_walk_recursive($data, function (&$item, $key) use ($keywordsLabelToId) {
            if (is_string($item)) {
                $item = str_replace(array_keys($keywordsLabelToId), $keywordsLabelToId, $item);
            }
        });
        return $data;
    }
}
