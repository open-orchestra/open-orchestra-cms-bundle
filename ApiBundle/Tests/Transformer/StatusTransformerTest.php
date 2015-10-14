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
    protected $transformerManager;
    protected $groupContext;
    protected $transformer;
    protected $translator;
    protected $status;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->authorizeStatusChangeManager = Phake::mock('OpenOrchestra\BackofficeBundle\StrategyManager\AuthorizeStatusChangeManager');
        $this->groupContext = Phake::mock('OpenOrchestra\BaseApi\Context\GroupContext');
        $this->translator = Phake::mock('OpenOrchestra\Backoffice\Manager\TranslationChoiceManager');
        $this->status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');

        $translationChoiceManager = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        $transformerManager = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        $statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        $roleRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface');
        $router = Phake::mock('Symfony\Component\Routing\RouterInterface');

        $statusId = 'StatusId';

        Phake::when($this->status)->getId()->thenReturn($statusId);
        Phake::when($router)->generateRoute(Phake::anyParameters())->thenReturn('route');
        Phake::when($statusRepository)->find(Phake::anyParameters())->thenReturn($this->status);
        Phake::when($transformerManager)->getGroupContext()->thenReturn($this->groupContext);
        Phake::when($transformerManager)->getRouter()->thenReturn($router);

        $this->transformer = new StatusTransformer($this->authorizeStatusChangeManager, $roleRepository, $this->translator, $translationChoiceManager);
        $this->transformer->setContext($transformerManager);
    }

    /**
     * @param bool $published
     * @param bool $initial
     * @param bool $isGranted
     * @param bool $hasGroup
     *
     * @dataProvider provideTransformData
     */
    public function testTransform($published, $initial, $isGranted, $hasGroup)
    {
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        $document = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        $attribute = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentAttributeInterface');
        $labels = Phake::mock('Doctrine\Common\Collections\Collection');
        $role = Phake::mock('OpenOrchestra\ModelBundle\Document\Role');

        $roles = new ArrayCollection();

        Phake::when($role)->getName()->thenReturn('role_name');

        $roles->add($role);

        Phake::when($this->authorizeStatusChangeManager)->isGranted(Phake::anyParameters())->thenReturn($isGranted);
        Phake::when($this->groupContext)->hasGroup(Phake::anyParameters())->thenReturn($hasGroup);
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn('trans_display_color');
        Phake::when($this->status)->getDisplayColor()->thenReturn('display_color');
        Phake::when($this->status)->isPublished()->thenReturn($published);
        Phake::when($this->status)->isInitial()->thenReturn($initial);
        Phake::when($this->status)->getFromRoles()->thenReturn($roles);
        Phake::when($this->status)->getToRoles()->thenReturn($roles);
        Phake::when($this->status)->getLabels()->thenReturn($labels);
        Phake::when($content)->getAttributes()->thenReturn(array($attribute, $attribute));

        $facade = $this->transformer->transform($this->status, $document);

        $this->assertInstanceOf('OpenOrchestra\BaseApi\Facade\FacadeInterface', $facade);
        $this->assertSame($published, $facade->published);
        $this->assertSame($initial, $facade->initial);
        $this->assertSame($isGranted, $facade->allowed);

        if (!$hasGroup) {
            $this->assertArrayHasKey('_self_delete', $facade->getLinks());
            $this->assertArrayHasKey('_self_form', $facade->getLinks());
        } else {
            $this->isEmpty($facade->getLinks());
        }
    }

    /**
     * @return array
     */
    public function provideTransformData()
    {
        return array(
            array(true, true, true, false),
            array(true, false, true, false),
            array(false, true, true, false),
            array(false, false, true, false),
            array(true, true, false, false),
            array(true, false, false, false),
            array(false, true, false, false),
            array(false, false, false, false),
            array(true, true, true, true),
            array(true, false, true, true),
            array(false, true, true, true),
            array(false, false, true, true),
            array(true, true, false, true),
            array(true, false, false, true),
            array(false, true, false, true),
            array(false, false, false, true),
        );
    }

    /**
     * Test Exception transform with wrong object a parameters
     */
    public function testExceptionTransform()
    {
        $this->setExpectedException('OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException');
        $this->transformer->transform(Phake::mock('stdClass'));
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $this->assertSame('status', $this->transformer->getName());
    }
}
