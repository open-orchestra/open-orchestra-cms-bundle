<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;

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
     * @param NodeInterface               $node
     * @param BlockNodePattern|Constraint $constraint
     */
    public function validate($node, Constraint $constraint)
    {
        if ($node->getStatus() instanceof StatusInterface && $node->getStatus()->isPublishedState()) {
            $areas = $node->getAreas();
            $routePattern = $node->getRoutePattern();
            foreach ($areas as $area) {
                $blocks = $area->getBlocks();
                foreach ($blocks as $block) {
                    $blockLabel = $block->getLabel();
                    $parameters = $this->generateFormManager->getRequiredUriParameter($block);
                    foreach ($parameters as $parameter) {
                        if (false === strpos($routePattern, '{' . $parameter . '}')) {
                            $this->context
                                ->buildViolation($constraint->message)
                                ->setParameters(array(
                                    '%blockLabel%' => $blockLabel,
                                    '%parameter%' => $parameter
                                ))
                                ->atPath('routePattern')
                                ->addViolation();
                        }
                    }
                }
            }
        }
    }
}
