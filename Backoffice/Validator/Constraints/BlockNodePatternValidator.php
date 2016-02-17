<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Model\AreaContainerInterface;

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
        if ($node->getStatus() instanceof StatusInterface && $node->getStatus()->isPublished()) {
            $blocks = $node->getBlocks();
            $blockReferences = $this->getRefBlock($node);
            $routePattern = $node->getRoutePattern();
            foreach ($blockReferences as $blockRef) {
                $block = $blocks[$blockRef];
                $parameters = $this->generateFormManager->getRequiredUriParameter($block);
                $blockLabel = $block->getLabel();
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

    /**
     * @param AreaContainerInterface $container
     *
     * @return array
     */
    protected function getRefBlock(AreaContainerInterface $container)
    {
        $blockRef = array();
        $areas = $container->getAreas();
        if ($container instanceof NodeInterface || count($areas) > 0){
            foreach ($areas as $area) {
                $blockRef = array_merge($blockRef, $this->getRefBlock($area));
            }
        } else {
            $blocks = $container->getBlocks();
            if (count($blocks) > 0){
                foreach ($blocks as $block) {
                    if($block['nodeId'] === 0) {
                        $blockRef[] = $block['blockId'];
                    }
                }
            }
        }

        return array_unique($blockRef);
    }
}
