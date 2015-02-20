<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\ModelInterface\Model\NodeInterface;
use PHPOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PHPOrchestra\DisplayBundle\Manager\TreeManager;

/**
 * Class OrchestraNodeType
 */
class OrchestraNodeChoiceType extends AbstractType
{
    protected $nodeRepository;
    protected $treeManager;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     */
    public function __construct(NodeRepositoryInterface $nodeRepository, TreeManager $treeManager)
    {
        $this->nodeRepository = $nodeRepository;
        $this->treeManager = $treeManager;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'choices' => $this->getChoices()
            )
        );
    }

    /**
     * @return array
     */
    protected function getChoices()
    {
        $nodes = $this->nodeRepository->findLastVersionBySiteId();
        $orderedNodes = $this->treeManager->generateTree($nodes);

        return $this->getRecChoices($orderedNodes);
    }

    /**
     * @return array
     */
    protected function getRecChoices($nodes, $depth = 0)
    {
        $choices = array();
        foreach ($nodes as $node) {
            $choices[$node['node']->getNodeId()] = str_repeat('--', $depth).' '.$node['node']->getName();
            if (array_key_exists('child', $node)) {
                $choices = array_merge($choices, $this->getRecChoices($node['child'], $depth++));
            }
        }

        return $choices;

    }
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'orchestra_node_choice';
    }

    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'choice';
    }
}
