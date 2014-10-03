<?php

namespace PHPOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use PHPOrchestra\BackofficeBundle\Form\Type\NodeType;
use PHPOrchestra\ModelBundle\Model\TemplateInterface;

/**
 * Description of NodeTypeTest
 */
class NodeTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $nodeType;
    protected $nodeClass = 'nodeClass';
    protected $templateRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->templateRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\TemplateRepository');
        $this->nodeType = new NodeType($this->nodeClass, $this->templateRepository);
    }

    /**
     * test the build form
     *
     * @param TemplateInterface $templates
     * @param array $expectedResult
     *        @dataProvider getTemplate
     */
    public function testBuildForm($templates, $expectedResult)
    {
        $formBuilderMock = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($formBuilderMock)->add(Phake::anyParameters())->thenReturn($formBuilderMock);

        Phake::when($this->templateRepository)->findByDeleted(Phake::anyParameters())->thenReturn($templates);

        $this->nodeType->buildForm($formBuilderMock, array());

        Phake::verify($formBuilderMock, Phake::times(7))->add(Phake::anyParameters());

        Phake::verify($formBuilderMock)->add('templateId', 'choice', array(
            'choices' => $expectedResult
        ));

        Phake::verify($formBuilderMock, Phake::never())->addModelTransformer(Phake::anyParameters());
        Phake::verify($formBuilderMock, Phake::times(3))->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * Test the default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->nodeType->setDefaultOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'data_class' => $this->nodeClass
        ));
    }

    /**
     * Test the form name
     */
    public function testGetName()
    {
        $this->assertEquals('node', $this->nodeType->getName());
    }

    /**
     * Templates provider
     *
     * @return array
     */
    public function getTemplate()
    {
        $id0 = 'fakeId0';
        $name0 = 'fakeName0';
        $id1 = 'fakeId1';
        $name1 = 'fakeName1';

        $template0 = Phake::mock('PHPOrchestra\ModelBundle\Model\TemplateInterface');
        Phake::when($template0)->getTemplateId()->thenReturn($id0);
        Phake::when($template0)->getName()->thenReturn($name0);

        $template1 = Phake::mock('PHPOrchestra\ModelBundle\Model\TemplateInterface');
        Phake::when($template1)->getTemplateId()->thenReturn($id1);
        Phake::when($template1)->getName()->thenReturn($name1);

        return array(
            array(
                array($template0, $template1), array($id0 => $name0, $id1 => $name1)
            )
        );
    }
}
