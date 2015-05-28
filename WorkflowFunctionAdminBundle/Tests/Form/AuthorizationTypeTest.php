<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\WorkflowFunctionAdminBundle\Form\Type\AuthorizationType;
use Phake;

/**
 * Description of AuthorizationTypeTest
 */
class AuthorizationTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AuthorizationType
     */
    protected $authorizationType;

    protected $contentTypeRepository;
    protected $translationChoiceManager;
    protected $authorizationClass = 'fakeClass';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->contentTypeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface');
        $this->translationChoiceManager = Phake::mock('OpenOrchestra\Backoffice\Manager\TranslationChoiceManager');

        $this->authorizationType = new AuthorizationType($this->contentTypeRepository, $this->translationChoiceManager, $this->authorizationClass);
    }

    /**
     * test default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->authorizationType->setDefaultOptions($resolverMock);

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

        Phake::verify($formBuilderInterface, Phake::times(2))->add(Phake::anyParameters());
    }

    /**
     * @param mixed  $contentType
     * @param string $contentTypeName
     *
     * @dataProvider provideContentTypeAndName
     */
    public function testBuildView($contentType, $contentTypeName = 'open_orchestra_backoffice.left_menu.editorial.nodes')
    {
        $formView = Phake::mock('Symfony\Component\Form\FormView');
        $form = Phake::mock('Symfony\Component\Form\FormInterface');
        $authorization = Phake::mock('OpenOrchestra\WorkflowFunctionBundle\Document\Authorization');
        Phake::when($authorization)->getReferenceId()->thenReturn('fakeReference');

        $formView->vars['value'] = $authorization;

        Phake::when($this->contentTypeRepository)->find(Phake::anyParameters())->thenReturn($contentType);
        Phake::when($this->translationChoiceManager)->choose(Phake::anyParameters())->thenReturn($contentTypeName);

        $this->authorizationType->buildView($formView, $form, array());

        $this->assertSame($contentTypeName, $formView->vars['label']);
    }

    /**
     * @return array
     */
    public function provideContentTypeAndName()
    {
        $contentType = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($contentType)->getNames()->thenReturn(new ArrayCollection());

        return array(
            array(null),
            array($contentType, 'contentTypeName'),
        );
    }

    /**
     * test Name
     */
    public function testGetName()
    {
        $this->assertEquals('authorization', $this->authorizationType->getName());
    }
}
