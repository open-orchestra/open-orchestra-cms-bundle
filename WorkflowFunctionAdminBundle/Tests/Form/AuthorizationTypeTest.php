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
    protected $contentTypeRepository;
    protected $translationChoiceManager;
    protected $authorizationClass = 'fakeClass';
    protected $authorizationType;

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
     * test Name
     */
    public function testGetName()
    {
        $this->assertEquals('authorization', $this->authorizationType->getName());
    }
}
