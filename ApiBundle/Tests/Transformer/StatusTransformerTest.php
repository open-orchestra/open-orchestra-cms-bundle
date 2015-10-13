<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BaseApi\Context\GroupContext;
use OpenOrchestra\BackofficeBundle\StrategyManager;
use Phake;
use OpenOrchestra\ApiBundle\Transformer\StatusTransformer;

/**
 * Class StatusTransformerTest
 */
class StatusTransformerTest extends \PHPUnit_Framework_TestCase
{
    protected $authorizeStatusChangeManager;
    protected $translationChoiceManager;
    protected $transformerManager;
    protected $roleRepository;
    protected $groupContext;
    protected $transformer;
    protected $translator;
    protected $statusable;
    protected $statusId;
    protected $content;
    protected $router;
    protected $status;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        $this->status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $this->statusable = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        $this->statusId = 'StatusId';
        Phake::when($this->status)->getId(Phake::anyParameters())->thenReturn($this->statusId);

        /** Set router */
        $this->router = Phake::mock('Symfony\Component\Routing\RouterInterface');
        Phake::when($this->router)->generateRoute(Phake::anyParameters())->thenReturn('route');

        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        Phake::when($this->statusRepository)->find(Phake::anyParameters())->thenReturn($this->status);

        /** Set Construct Params */
        $this->authorizeStatusChangeManager = Phake::mock('OpenOrchestra\BackofficeBundle\StrategyManager\AuthorizeStatusChangeManager');
        $this->roleRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface');
        $this->translator = Phake::mock('OpenOrchestra\Backoffice\Manager\TranslationChoiceManager');
        $this->translationChoiceManager = Phake::mock('Symfony\Component\Translation\TranslatorInterface');

        /** Set Context */
        $this->groupContext = Phake::mock('OpenOrchestra\BaseApi\Context\GroupContext');
        $this->transformerManager = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        Phake::when($this->transformerManager)->getGroupContext()->thenReturn($this->groupContext);
        Phake::when($this->transformerManager)->getRouter()->thenReturn($this->router);

        /** init Transformer */
        $this->transformer = new StatusTransformer($this->authorizeStatusChangeManager, $this->roleRepository, $this->translator, $this->translationChoiceManager);
        $this->transformer->setContext($this->transformerManager);

    }

    /**
     * @dataProvider provideTransformData
     */
    public function testTransform($published, $initial, $isGranted)
    {
        Phake::when($this->authorizeStatusChangeManager)->isGranted(Phake::anyParameters())->thenReturn($isGranted);
        $document = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');

        Phake::when($this->groupContext)->hasGroup(Phake::anyParameters())->thenReturn(false);
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn(true);
        Phake::when($this->status)->getDisplayColor(Phake::anyParameters())->thenReturn(true);

        $roles = new ArrayCollection();
        $role = Phake::mock('OpenOrchestra\ModelBundle\Document\Role');
        Phake::when($role)->getName()->thenReturn(true);
        $roles->add($role);

        $labels = Phake::mock('Doctrine\Common\Collections\Collection');
        Phake::when($this->status)->getFromRoles()->thenReturn($roles);
        Phake::when($this->status)->getToRoles()->thenReturn($roles);
        Phake::when($this->status)->getLabels()->thenReturn($labels);

        $facade = Phake::mock('OpenOrchestra\BaseApi\Facade\FacadeInterface');
        $facade->label = 'draft';
        $facade->name = 'fakeName';
        $facade->value = 'fakeValue';
        $facade = $this->transformer->transform($this->status);

        Phake::when($this->status)->isPublished()->thenReturn($published);
        Phake::when($this->status)->isInitial()->thenReturn($initial);

        $attribute = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentAttributeInterface');
        Phake::when($this->content)->getAttributes()->thenReturn(array($attribute, $attribute));

        $facade = $this->transformer->transform($this->status, $document);

        $this->assertInstanceOf('OpenOrchestra\BaseApi\Facade\FacadeInterface', $facade);
        $this->assertArrayHasKey('_self_delete', $facade->getLinks());
        $this->assertArrayHasKey('_self_form', $facade->getLinks());

        $this->assertSame($published, $facade->published);
        $this->assertSame($initial, $facade->initial);
        $this->assertSame($isGranted, $facade->allowed);
        Phake::verify($this->groupContext, Phake::atLeast(0))->hasGroup(GroupContext::G_HIDE_ROLES);
        Phake::verify($this->translator, Phake::atLeast(0))->trans('open_orchestra_backoffice.form.status.color.');
        Phake::verify($this->status, Phake::atLeast(0))->getDisplayColor('open_orchestra_backoffice.form.status.color.');
    }

    /**
     * @return array
     */
    public function provideTransformData()
    {
        return array(
            array(true, true, true),
            array(true, false, true),
            array(false, true, true),
            array(false, false, true),
            array(true, true, false),
            array(true, false, false),
            array(false, true, false),
            array(false, false, false),
        );
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $this->assertSame('status', $this->transformer->getName());
    }
}