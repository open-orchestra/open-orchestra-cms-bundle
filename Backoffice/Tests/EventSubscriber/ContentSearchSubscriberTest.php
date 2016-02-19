<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\EventSubscriber\ContentSearchSubscriber;
use Symfony\Component\Form\FormEvents;

/**
 * Class ChangeNodeContentSubscriberTest
 */
class ContentSearchSubscriberTest extends AbstractBaseTestCase
{
    protected $subscriber;
    protected $contentRepository;
    protected $contextManager;
    protected $transformer;
    protected $attributes;
    protected $form;
    protected $contentName1 = 'fakeContentName1';
    protected $contentId1 = 'fakeContentId1';
    protected $contentName2 = 'fakeContentName2';
    protected $contentId2 = 'fakeContentId2';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $contentType = 'fakeContentType';
        $choiceType = 'fakeChoiceType';
        $keywords = 'fakeKeywords';
        $language = 'fakeLanguage';

        $condition = '{fakeCondition: "fakeCondition"}';
        $jCondition = json_decode($condition, true);

        $content1 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content1)->getName()->thenReturn($this->contentName1);
        Phake::when($content1)->getId()->thenReturn($this->contentId1);
        $content2 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content2)->getName()->thenReturn($this->contentName2);
        Phake::when($content2)->getId()->thenReturn($this->contentId2);
        $this->contentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');

        Phake::when($this->contentRepository)->findByContentTypeAndCondition($language, $contentType, $choiceType, $jCondition)->thenReturn(array(
                $content1,
                $content2
        ));
        $this->contextManager = Phake::mock('OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface');
        Phake::when($this->contextManager)->getCurrentSiteDefaultLanguage()->thenReturn($language);

        $this->transformer = Phake::mock('OpenOrchestra\Transformer\ConditionFromBooleanToBddTransformerInterface');
        Phake::when($this->transformer)->reverseTransform($keywords)->thenReturn($condition);

        $this->attributes = array(
            'fakeAttributes' => 'fakeAttributes'
        );

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        $this->form = Phake::mock('Symfony\Component\Form\FormInterface');

        Phake::when($this->event)->getForm()->thenReturn($this->form);

        Phake::when($this->event)->getData()->thenReturn(array(
            'contentType' => $contentType,
            'choiceType' => $choiceType,
            'keywords' => $keywords,
        ));

        $this->subscriber = new ContentSearchSubscriber(
            $this->contentRepository,
            $this->contextManager,
            $this->transformer,
            $this->attributes
        );
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * @param string $eventName
     *
     * @dataProvider provideSubscribedEvent
     */
    public function testEventSubscribed($eventName)
    {
        $this->assertArrayHasKey($eventName, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @return array
     */
    public function provideSubscribedEvent()
    {
        return array(
            array(FormEvents::PRE_SUBMIT),
        );
    }

    /**
     * Test preSubmit
     */
    public function testPreSubmit()
    {
        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->form)->add('contentId', 'choice', array(
                    'label' => false,
                    'required' => false,
                    'choices' => array(
                        $this->contentId1 => $this->contentName1,
                        $this->contentId2 => $this->contentName2,
                   ),
                    'attr' => $this->attributes,
            ));
    }
}
