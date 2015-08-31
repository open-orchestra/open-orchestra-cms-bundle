<?php

namespace OpenOrchestra\BackofficeBundle\Validator\Constraints;

use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class RestoreNodeValidator
 */
class RestoreNodeValidator extends ConstraintValidator
{
    protected $nodeRepository;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     */
    public function __construct(NodeRepositoryInterface $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * @param NodeInterface $node
     * @param Constraint    $constraint
     */
    public function validate($node, Constraint $constraint)
    {
        if ($node->getNodeType() === NodeInterface::TYPE_DEFAULT && $node->getNodeId() !== NodeInterface::ROOT_NODE_ID) {
            $parentId = $node->getParentId();
            $parent = $this->nodeRepository->findOneByNodeId($parentId);

            if (null === $parent || $parent->isDeleted()) {
                $nameParent = (null === $parent) ? '' : $parent->getName();
                $this->context->buildViolation($constraint->message)
                    ->setParameters(array(
                        '%nodeparent%' => $nameParent,
                    ))
                    ->addViolation();
            }
        }
    }

}
