<?php

namespace OpenOrchestra\WorkflowAdminBundle\Tests\Form\Type\Component;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\WorkflowAdminBundle\Form\Type\Component\AuthorizationType;
use Phake;

/**
 * Description of AuthorizationTypeTest
 */
class AuthorizationTypeTest extends AbstractBaseTestCase
{
    /**
     * @var AuthorizationType
     */
    protected $authorizationType;

    protected $contentTypeRepository;
    protected $authorizationClass = 'fakeClass';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->contentTypeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface');

        $this->authorizationType = new AuthorizationType($this->contentTypeRepository, $this->authorizationClass);
    }

    /**
     * test default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->authorizationType->configureOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(
            array('data_class' => $this->authorizationClass)
        );
    }

    /**
     * test buildForm
     */
    public function testBuildForm()
    {
        $formBuilderInterface = Phake::mock('Symfony\Component\Form\FormBuilderInterface');

        $this->authorizationType->buildForm($formBuilderInterface, array());

        Phake::verify($formBuilderInterface, Phake::times(3))->add(Phake::anyParameters());
    }

    /**
     * @param mixed  $contentType
     * @param mixed $contentTypeName
     *
     * @dataProvider provideContentTypeAndName
     */
    public function testBuildView($contentType, $contentTypeName = 'open_orchestra_backoffice.left_menu.editorial.nodes')
    {
        $formView = Phake::mock('Symfony\Component\Form\FormView');
        $form = Phake::mock('Symfony\Component\Form\FormInterface');
        $authorization = Phake::mock('OpenOrchestra\Workflow\Model\AuthorizationInterface');
        $formView->vars['value'] = $authorization;

        Phake::when($this->contentTypeRepository)->find(Phake::anyParameters())->thenReturn($contentType);

        $this->authorizationType->buildView($formView, $form, array());

        $this->assertSame($contentTypeName, $formView->vars['label']);
    }

    /**
     * @return array
     */
    public function provideContentTypeAndName()
    {
        $names = new ArrayCollection();
        $contentType = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($contentType)->getNames()->thenReturn($names);

        return array(
            array(null),
            array($contentType, $names),
        );
    }

    /**
     * test Name
     */
    public function testGetName()
    {
        $this->assertEquals('oo_authorization', $this->authorizationType->getName());
    }
}
