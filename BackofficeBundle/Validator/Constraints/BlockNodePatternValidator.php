<?php

namespace OpenOrchestra\BackofficeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Model\AreaContainerInterface;

/**
 * Class BlockNodePatternValidator
 */
class BlockNodePatternValidator extends ConstraintValidator
{
    protected $generateFormManager;
    protected $session;
    protected $templating;
    protected $translator;
    protected $blockRef = array();

    /**
     * @param GenerateFormManager $generateFormManager
     * @param SessionInterface    $session
     * @param EngineInterface     $templating
     * @param TranslatorInterface $translator
     */
    public function __construct(GenerateFormManager $generateFormManager, Session $session, EngineInterface $templating, TranslatorInterface $translator)
    {
        $this->generateFormManager = $generateFormManager;
        $this->session = $session;
        $this->templating = $templating;
        $this->translator = $translator;
    }

    /**
     * @param NodeInterface $node
     * @param Constraint $constraint
     */
    public function validate($node, Constraint $constraint)
    {
        if ($node->getStatus() instanceof StatusInterface && $node->getStatus()->isPublished()) {
            $blocks = $node->getBlocks();
            $this->getRefBlock($node);
            $routePattern = $node->getRoutePattern();
            $isValid = true;
            foreach ($this->blockRef as $blockRef) {
                $block = $blocks[$blockRef];
                $parameters = $this->generateFormManager->getRequiredUriParameter($block);
                $blockLabel = $block->getLabel();
                foreach ($parameters as $parameter) {
                    if (false === strpos($routePattern, '{' . $parameter . '}')) {
                        $this->session->getFlashBag()->add('alert',
                            $this->translator->trans('open_orchestra_backoffice.form.node.error.pattern', array(
                                '%blockLabel%' => $blockLabel,
                                '%parameter%' => $parameter))
                        );
                        $isValid = false;
                    }
                }
            }
            if (!$isValid) {
                $response = $this->templating->render('BraincraftedBootstrapBundle::flash.html.twig');
                $this->context->buildViolation($response)
                    ->atPath('BlockNodePattern')
                    ->addViolation();
            }
        }
    }
    protected function getRefBlock(AreaContainerInterface $container)
    {
        $areas = $container->getAreas();
        if (count($areas) > 0){
            foreach ($areas as $area) {
                $this->getRefBlock($area);
            }
        } else {
            $blocks = $container->getBlocks();
            if (count($blocks) > 0){
                foreach ($blocks as $block) {
                    if($block['nodeId'] !== NodeInterface::TRANSVERSE_NODE_ID) {
                        $this->blockRef[] = $block['blockId'];
                    }
                }
            }
        }
    }
}
