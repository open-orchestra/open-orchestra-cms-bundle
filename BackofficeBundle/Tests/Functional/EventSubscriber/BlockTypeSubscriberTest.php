<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Functional\EventSubscriber;

use OpenOrchestra\BackofficeBundle\Tests\Functional\AbstractAuthentificatedTest;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ConfigurableContentStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ContentListStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\SampleStrategy;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\VideoStrategy;
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

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
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
        $formType =  static::$kernel->getContainer()->get('open_orchestra_backoffice.generate_form_manager')->createForm($block);
        $form = $this->formFactory->create($formType, $block, array('csrf_protection' => false));

        $form->submit(array(
            'id' => 'testId',
            'class' => 'testClass',
            'videoType' => 'youtube',
            'youtubeVideoId' => 'videoId',
            'youtubeAutoplay' => true,
        ));
        $this->assertTrue($form->isSynchronized());
        /** @var BlockInterface $data */
        $data = $form->getConfig()->getData();
        $this->assertBlock($data);
        $this->assertSame('videoId', $data->getAttribute('youtubeVideoId'));
        $this->assertTrue($data->getAttribute('youtubeAutoplay'));
        $this->assertFalse($data->getAttribute('youtubeFs'));
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

        $formType =  static::$kernel->getContainer()->get('open_orchestra_backoffice.generate_form_manager')->createForm($block);
        $form = $this->formFactory->create($formType, $block, array('csrf_protection' => false));

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
            array(SampleStrategy::NAME, array(
                'title' => 'title',
                'news' => 'news',
                'author' => 'author',
                'multipleChoice' => array('foo', 'none'),
            )),
            array(ContentListStrategy::NAME, array(
                'contentNodeId' => 'root',
                'contentSearch' => array(
                    'contentType' => 'news',
                    'choiceType' => 'choice_and',
                    'keywords' => null,
                ),
                'characterNumber' => 150,
                'contentTemplateEnabled' => true,
            )),
            array(ConfigurableContentStrategy::NAME, array(
                'contentSearch' => array(
                    'contentType' => 'car',
                    'choiceType' => ReadContentRepositoryInterface::CHOICE_AND,
                    'keywords' => null,
                    'contentId' => null,
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

        $formType =  static::$kernel->getContainer()->get('open_orchestra_backoffice.generate_form_manager')->createForm($block);
        $form = $this->formFactory->create($formType, $block, array('csrf_protection' => false));

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
                        'contentNodeId' => 'root',
                        'contentTemplateEnabled' => true,
                        'contentSearch' => array(
                                'contentType' => 'news',
                                'choiceType' => ReadContentRepositoryInterface::CHOICE_AND,
                                'keywords' => 'Lorem AND Ipsum',
                            )
                ), array(
                        'contentNodeId' => 'root',
                        'contentTemplateEnabled' => true,
                        'contentSearch' => array(
                                'contentType' => 'news',
                                'choiceType' => ReadContentRepositoryInterface::CHOICE_AND,
                                'keywords' => '{"$and":[{"keywords":{"$eq":"Lorem"}},{"keywords":{"$eq":"Ipsum"}}]}'
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
}
