<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Validator\Constraints;

use Phake;

use OpenOrchestra\BackofficeBundle\Validator\Constraints\BlockNodePatternValidator;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class BlockNodePatternValidatorTest
 */
class BlockNodePatternValidatorTest extends \PHPUnit_Framework_TestCase
{
    protected $validator;
    protected $constraint;
    protected $context;
    protected $constraintViolationBuilder;
    protected $flashBag;
    protected $session;
    protected $templating;
    protected $translator;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = Phake::mock('Symfony\Component\Validator\Constraint');
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContextInterface');
        $this->constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');
        $this->generateFormManager = Phake::mock('OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager');
        $this->flashBag = Phake::mock('Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface');
        $this->session = Phake::mock('Symfony\Component\HttpFoundation\Session\Session');
        $this->templating = Phake::mock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');

        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);
        Phake::when($this->constraintViolationBuilder)->atPath(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);
        Phake::when($this->session)->getFlashBag()->thenReturn($this->flashBag);

        $this->validator = new BlockNodePatternValidator($this->generateFormManager, $this->session, $this->templating, $this->translator);
        $this->validator->initialize($this->context);
    }

    /**
     * Test instance
     */
    public function testClass()
    {
        $this->assertInstanceOf('Symfony\Component\Validator\ConstraintValidator', $this->validator);
    }

    /**
     * @param NodeInterface $node
     * @param array         $parameter
     * @param int           $violationTimes
     * @param int           $flashBagTimes
     *
     * @dataProvider provideCountAndViolation
     */
    public function testAddViolationOrNot(NodeInterface $node, array $parameter, $violationTimes, $flashBagTimes)
    {
        $blocks = $node->getBlocks();
        foreach ($blocks as $key => $block) {
            Phake::when($this->generateFormManager)->getRequiredUriParameter($block)->thenReturn($parameter[$key]);
        }

        $this->validator->validate($node, $this->constraint);

        Phake::verify($this->context, Phake::times($violationTimes))->buildViolation(Phake::anyParameters());

        Phake::verify($this->flashBag, Phake::times($flashBagTimes))->add(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideCountAndViolation()
    {
        $fakeId = 'fakeId';

        $block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($block)->getLabel()->thenReturn('fakeLabel');

        $blocks0 = new ArrayCollection();
        $blocks0->add($block);
        $node0 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node0)->getRoutePattern()->thenReturn('');
        Phake::when($node0)->getBlocks()->thenReturn($blocks0);

        $blocks1 = new ArrayCollection();
        $blocks1->add($block);
        $node1 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node1)->getRoutePattern()->thenReturn('{' . $fakeId . '}');
        Phake::when($node1)->getBlocks()->thenReturn($blocks1);

        $blocks2 = new ArrayCollection();
        $blocks2->add($block);
        $node2 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node2)->getRoutePattern()->thenReturn('');
        Phake::when($node2)->getBlocks()->thenReturn($blocks2);

        $blocks3 = new ArrayCollection();
        $blocks3->add($block);
        $blocks3->add($block);
        $node3 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node3)->getRoutePattern()->thenReturn('');
        Phake::when($node3)->getBlocks()->thenReturn($blocks3);

        return array(
            array($node0, array(array()), 0, 0),
            array($node1, array(array($fakeId)), 0, 0),
            array($node2, array(array($fakeId)), 1, 1),
            array($node3, array(array($fakeId), array($fakeId)), 1, 2),
        );
    }
}
