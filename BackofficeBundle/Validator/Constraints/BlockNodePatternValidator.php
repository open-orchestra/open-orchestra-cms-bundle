<?php

namespace OpenOrchestra\BackofficeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class BlockNodePatternValidator
 */
class BlockNodePatternValidator extends ConstraintValidator
{
    protected $generateFormManager;

    /**
     * @param GenerateFormManager $generateFormManager
     */
    public function __construct(GenerateFormManager $generateFormManager)
    {
        $this->generateFormManager = $generateFormManager;
    }

    /**
     * @param NodeInterface $node
     * @param Constraint $constraint
     */
    public function validate($node, Constraint $constraint)
    {
        $blocks = $node->getBlocks();
        $routePattern = $node->getRoutePattern();
        foreach ($blocks as $block) {
            $parameters = $this->generateFormManager->getRequiredUriParameter($block);
            $blockLabel = $block->getLabel();
            foreach ($parameters as $parameter) {
                if (false === strpos($routePattern, '{' . $parameter . '}')) {
                    $this->context->buildViolation($constraint->message, array('%blockLabel%' => $blockLabel, '%parameter%' => $parameter))
                        ->atPath('BlockNodePattern')
                        ->addViolation();
                }
            }
        }
    }
}
